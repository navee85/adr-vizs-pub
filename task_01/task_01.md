# 1. Vizsgafeladat – Könyvelő iroda deaktiválásának kezelése

---

A feladat átolvasása és a kód átnézése után a válaszaimat két főbb részre bontanám:

1. Review eredménye
2. Általános véleményezés

---

# 1. Review eredménye

Ebben a részben a működésbeli problémákra fogok fókuszálni. A célom az hogy alapul véve a mostani kódkörnyezetet az újonnan bekerült kódrészletek által a feladatban kiírt célok teljesüljenek a nem érintett kódrészletek módosítása nélkül.

## 💡 Észrevételek

### 35.sor:

```php
protected const PAYMENT_OVERDUE_GRACE_THRESHOLD = 4; // in days
```

A feladat kiírás szerint: *"Ez az eltolási érték az API-n keresztül érkezik egy payment_overdue_grace_threshold nevű mezőben."* - itt viszont konstansként van definiálva, ami nem jó.

**🛠️ Megoldási javaslat:**

Ahogy látom `execute()` metódusban a response body-ban érkező tartalomból kellene kivenni a `payment_overdue_grace_threshold` értékét, és belerakni egy osztály változóba, például:

```php
private int $paymentOverdueGraceThreshold;
```

*(A review további részében erre a változóra fogok hivatkozni)*

---

### 117. sor

A `setIsDeactivated()` metódus második argumentuma a `$salesStatus`, ha ennek az értéke - a feladat kiírás szerint - *Szerződést bontott*, akkor nincs szükség további ellenőrzésre. Ez it nem történik meg, hanem egyből elindul a kifizetetlen számlák ellenőrzése.

**🛠️ Megoldási javaslat:**

A vizsgálat elsőként fusson le a `setIsDeactivated()` hívásakor:

```php
protected function setIsDeactivated(KonyveloIroda $client, string $salesStatus): void
{
    if (strcasecmp($salesStatus,self::CRM_STATUS_CONTRACT_CANCELLED) === 0) {
        $client->setIsDeactivated(true);
        return;
    }
    ...
}
```

Viszont így redundáns lesz az ellenőrzés a kicsit lejjebb lévő résznél:

```php
if (
    strcasecmp($salesStatus, self::CRM_STATUS_CONTRACT_CANCELLED) === 0 ||
    strcasecmp($salesStatus, self::CRM_STATUS_CONTRACT_SUSPENDED) === 0
) {
    $client->setIsDeactivated(true);
}
```

Így ezt a részt erre változtatnám:

```php
if (
    strcasecmp($salesStatus, self::CRM_STATUS_CONTRACT_SUSPENDED) === 0
) {
    $client->setIsDeactivated(true);
}
```

---

### 120. sor

Látok egy sorrend változtatást:

Eredeti:

```php
$hasUnpaidInvoices = $client->getKifizetettlenInfo() &&
    $this->checkHasUnpaidInvoices($client) &&
    $client->getRemainActiveUntil() < new DateTimeImmutable('now');
```

Megváltoztatott:

```php
$hasUnpaidInvoices = $client->getKifizetettlenInfo() &&
    $client->getRemainActiveUntil() < new DateTimeImmutable('now') &&
    $this->checkHasUnpaidInvoices($client);
```

Nem pontoson értem a változtatás miértjét és az összefüggéseket: A `getRemainActiveUntil()` feltételezem visszaad egy dátumot, és a feltétel az, hogy a felhasználó már inaktív. 

A `$hasUnpaidInvoices` változó éréke akkor lesz `true`, ha együtt érvényesül:
- Van kifizetetlen információ
- Az aktív státusz lejárt
- Vannak lejárt tartozások

**🛠️ Megoldási javaslat:**

Véleményem szerint elegendő, ha csak a kifizetetlen információ meglétét, valamint hogy van e kifizetetlen számla ezeket ellenrőizzük:

```php
$hasUnpaidInvoices = $client->getKifizetettlenInfo() &&
    $this->checkHasUnpaidInvoices($client);
```

*(esetleg külön ellenőrizném hogy a felhasználó aktív e)*

---

### 133. sor

Rossz a validálási sorrend

**🛠️ Megoldási javaslat:**

Ezt a részt:

```php
if ($hasUnpaidInvoices) {
    $client->setIsDeactivated(true);
}
```

... feljebb vinném, közvetlenül a `$hasUnpaidInvoices` változó alá, valamint egy `return`-t is írnék még hozzá, mivel a többi kritérium már nem lesz fontos:

```php
protected function setIsDeactivated(KonyveloIroda $client, string $salesStatus): void
{
    ...
    $hasUnpaidInvoices = $client->getKifizetettlenInfo() &&
        $client->getRemainActiveUntil() < new DateTimeImmutable('now') &&
        $this->checkHasUnpaidInvoices($client);
    
    if ($hasUnpaidInvoices) {
        $client->setIsDeactivated(true);
        return;
    }
    ...
```

---

### 141. sor

A *checkHasUnpaidInvoices()* metódusban lévő implementáció:

```php
private function checkHasUnpaidInvoices(KonyveloIroda $client): bool
{
    foreach ($client->getKifizetettlenInfo() as $kifizetettlenInfo) {
        if (
            new DateTimeImmutable($kifizetettlenInfo['Fizetési határidő'])
            < new Carbon::create()->addDays(self::PAYMENT_OVERDUE_GRACE_THRESHOLD)
        ) {
            return true;
        }
    }
    return false;
}
```

Itt több problémát is látok:

1. Carbon
   1. Szintaktikailag ez helytelen: `new Carbon::create();` 
      1. vagy `new Carbon()`
      2. vagy `Carbon::create()`
   2. Ha `Carbon::create()`-ot hívunk, akkor explicit kell átadni argumentumként az év, hónap, stb. értékeket. Példa: `Carbon::create(2024, 12, 13, 10, 15, 7, 'GMT')`. De itt nem erre van szükség.
   3. Amire nekünk szükség van az a `now()`, ami a pillanatnyi dátumot és időt adja vissza. Amennyiben nincs beállítva a Carbonnak globálisan az időzóna, akkor a `now()`-nak még meg kell adni a használt időzőnát, pl.: `Carbon::now('Europe/Budapest')`
 
2. Többféle dátum kezelő
   1. Egyazon célra - dátum összehasonlítás - két megoldást is van használva a kódban: `DateTimeImmutable` és `Carbon`. Én ezt kerülném, vagy az egyiket vagy a másikat használnám. De melyiket? 
   2. Számomra a Carbon sokkal szimpatikusabb, a `DateTime` és a `DateTimeImmutable` felé van építve mint wrapper réteg és egy csomó funkcióval bővíti. Viszont a kód többi részében a `DateTimeImmutable` van használva.
   3. Ha cél hogy a Carbon-t használjuk hosszútávon, akkor úgy engedném be a kódot, hogy az összehasonlítás mindkét oldalára Carbon alapú dátumot helyeztetnék: 
   ```php
   Carbon::parse($kifizetettlenInfo['Fizetési határidő']) < Carbon::now('Europe/Budapest')->addDays($this->paymentOverdueGraceThreshold)`;
   ```
      *(itt fontos lenne tudni, hogy a `$kifizetettlenInfo['Fizetési határidő']` milyen időzónában van.)*
   4. Továbbá ha cél a Carbon használata, akkor kiírnék egy Tech debt ticketet, amiben a `Carbon` bevezetésével kapcsolatos részleteket megvizsgálnám (hol használjuk, időzónák, formátumok stb.)

3. Kicsit "olvasmányosabbra" fognám a kódot, a feltétel kiszervezésével.

**🛠️ Megoldási javaslat:**

A fent említett problémákra az alábbi kódot javasolnám:

```php
private function checkHasUnpaidInvoices(KonyveloIroda $client): bool
{
    $graceDate = Carbon::now('Europe/Budapest')->addDays(self::PAYMENT_OVERDUE_GRACE_THRESHOLD);

    foreach ($client->getKifizetettlenInfo() as $kifizetettlenInfo) {
        if ($this->isDateBeforeGracePeriod($kifizetettlenInfo['Fizetési határidő'], $graceDate)) {
            return true;
        }
    }
    return false;
}

private function isDateBeforeGracePeriod(string $date, Carbon $graceDate): bool
{
    $comparisonDate = Carbon::parse($date, 'Europe/Budapest');
    return $comparisonDate < $graceDate;
}
```

---

## 2. Általános véleményezés

Ebben a pontban leírom a rendelkezésre bocsátott osztály alapján milyen optimalizálási lehetőségeket látok - teljes rendszer ismerete nélkül.

- Nevezéktan konvenció problémák (keveredik a magyar és az angol)
- PHPDoc és annotációs hiányok
- `getenv()` használatát nem javaslom, helyettük config-ból (vagy ennek Symfony-s megfelelőjéből) való olvasás lenne helyénvaló
- némely konstansok helyett enum-ot használnék (amennyiben PHP 8.1+-al dolgozunk)
- szimpla `array` helyett argumentumként DTO-kkal dolgoznék, ezzel jobban lenne látható a bejövő adatok struktúrája és típusai
- néhány helyen nehezebben olvasható a kód. Pl. az `if (... && ... && ...)` helyett egy jó beszédes nevű metódust használnék, és abba raknám be a logikai ellenőrzést 
- `getKifizetettlenInfo` -> `getKifizetetlenInfo` (-1 db "t") 😊

**Ezen problémák egy része már kiszűrhető review előtt, ha:**

- vannak tesztek a kódhoz
- kódanalizátor megoldások használatával (pl. PHPCS Fixer, PHP Codesniffer, PHPStan stb)
- ezek az eszközök be vannak kötve mondjuk egy prepush/precommit hook-ra

