# 3. Vizsgafeladat: Fejlesztési feladatok megírása – Megrendelés állapot szinkronizációs szolgáltatás

A rendszer bővítéséhez az alábbi ötleteim lennének:

**1. Automatikus értesítési rendszer hibák esetére**
- **Probléma:** A jelenlegi rendszer hibáinak kezeléséhez csak logok állnak rendelkezésre, amelyek manuális ellenőrzést igényelnek.
- **Megoldás:** Készíteni egy értesítési rendszert, amely automatikusan figyelmezteti az üzemeltetőket kritikus hibák esetén.
- **Hozzájárulás:** Gyorsabb hibakeresést tesz lehetővé, csökkenti a rendszerleállások időtartamát és növeli a megbízhatóságot.

**2. Részletes szinkronizációs riportok generálása**
- **Probléma:** A hibák és problémák jelenleg csak logokban érhetők el, ami megnehezíti az átláthatóságot és a hibajavítást.
- **Megoldás:** Rendszeresen generáljunk egy átfogó riportot a sikeres és sikertelen szinkronizációkról, tranzakciókról, és állapotfrissítésekről. A riport tartalmazza a hibák részleteit és a javasolt megoldásokat.
- **Hozzájárulás:** Jobb átláthatóságot biztosít és egyszerűsíti a hibakezelést.

**3. Párhuzamos feldolgozás bevezetése**
- **Probléma:** A jelenlegi folyamatok szekvenciálisan futnak, ami nagy adatállomány esetén megnövelheti a szinkronizációs időt.
- **Megoldás:** Használjunk queue-kat és aszinkron feldolgozást a tranzakciók és rendelések párhuzamos feldolgozására. A folyamatokat több worker dolgozza fel egyszerre.
- **Hozzájárulás:** Jelentősen csökkenti a szinkronizáció időtartamát, növeli a teljesítményt és a rendszer válaszidejét.


# Feladatok

## 1. Automatikus értesítési rendszer hibák esetére

### Description

A cél egy olyan értesítési rendszer kidolgozása, amely biztosítja, hogy a szinkronizáció során fellépő hibákról azonnal értesüljön az illetékes fejlesztői csapat vagy rendszergazda. Az értesítések célja, hogy a hibák gyorsan felismerhetők és kezelhetők legyenek, csökkentve a szolgáltatáskiesés vagy adatszinkronizációs problémák kockázatát.

Az értesítési rendszer a következő eseményekről küld értesítést:

- API-hívások sikertelensége
- Adatbázis mentési hibák
- Párhuzamos feldolgozás során fellépő kivételek
- Bármilyen olyan kritikus esemény, amelyet a SyncLogger critical szintű logként rögzít 

Az értesítések a következő csatornákon keresztül történnek:

- Email (Fejlesztői csapat részére)
- Slack értesítés (Rendszergazda vagy monitoring rendszer részére)

### Tasks

#### 1. Értesítési rendszer architektúrájának megtervezése
- Tervezd meg az értesítési rendszer architektúráját, figyelembe véve a következő csatornákat: email, Slack 
- Készíts konfigurációs opciókat az értesítési csatornák engedélyezésére/kikapcsolására

#### 2. Értesítések integrálása
- Használj Laravel Notification osztályokat az értesítések egyszerű kezelésére
- Hozz létre külön értesítési típusokat (pl. EmailNotification, SlackNotification) 

#### 3. Értesítések beépítése a logolási folyamatba
- A SyncLogger critical és error szintű logjaihoz kapcsolj automatikus értesítéseket
- Alakítsd ki az értesítések tartalmát úgy, hogy tartalmazza a hiba leírását, idejét, valamint az érintett folyamat azonosítóját

#### 4. Csatornánként specifikus formázás hozzáadása
- Készíts csatornánként eltérő formátumú üzeneteket (emailhez részletesebb leírás, Slack-hez tömörebb, jól strukturált üzenet) 
   
#### 5. Tesztelés
- Írj unit és integrációs teszteket

#### 6. Dokumentálés
- Dokumentáld az elkészült fejlesztést

### Acceptance criterias

- [ ] Az értesítési rendszer támogatja az email és Slack csatornákat
- [ ] A SyncLogger critical szintű logjainál automatikusan generálódik értesítés
- [ ] Az értesítési rendszer konfigurálható és könnyen bővíthető új csatornákkal
- [ ] Minden komponenshez unit és integrációs tesztek készültek
- [ ] Dokumentáció elkészült

### Additional Notes

- Érdemes lehet queue-t használni az értesítések aszinkron kezeléséhez
- Az értesítéseket JSON formában küldd el a különböző csatornák felé, így könnyen feldolgozható a monitoring eszközök számára

---

## 2. Részletes szinkronizációs riportok generálása

### Description

Implementálj egy részletes riport generáló rendszert a szinkronizációs folyamatokhoz. A cél, hogy átlátható és könnyen feldolgozható riportot készítsünk a szinkronizáció eredményeiről, beleértve a következő adatokat:

- Sikeresen feldolgozott tranzakciók/megrendelések száma
- Sikertelen feldolgozások (hibás tranzakciók/megrendelések) száma és részletei
- Duplikált tranzakciók azonosítása és részletei
- Szinkronizációs időtartam (mikor indult és mikor ért véget a folyamat)

### Tasks

#### 1. Riport struktúra kidolgozása:
  - Tervezd meg a riport schema-t, tartalmazza az összes releváns mezőt (pl. processed_count, failed_count, duplicated_count, start_time, end_time, stb.)

#### 2. Riport adatok gyűjtése:
  - Bővítsd a szinkronizációs szolgáltatásokat (pl. ConnectSyncService, WebshopSyncService, PairingService) úgy, hogy összegyűjtsék a riport számára szükséges adatokat
  - Implementálj olyan logikát, amely a riportban feltünteti a hibás tranzakciókat/megrendeléseket, és hogy milyen hibák történtek

#### 3. Riport generálása:
  - Készíts egy külön szolgáltatást (pl. Report/FormatService), amely a gyűjtött adatokat formázza és generálja a riportot CSV/JSON formátumra
  - Gondoskodj arról, hogy a riportot valamilyen módon (fájl, adatbázis, log) el lehessen érni

#### 4. Tesztelés
- Írj unit és integrációs teszteket

#### 5. Dokumentálés
- Dokumentáld az elkészült fejlesztést

### Acceptance criterias

- [ ] A riport tartalmazza a következő mezőket:
  - [ ] Sikeres tranzakciók száma (processed_count)
  - [ ] Sikertelen tranzakciók száma (failed_count)
  - [ ] Duplikált tranzakciók száma (duplicated_count)
  - [ ] Szinkronizáció kezdete és vége (start_time, end_time)
- [ ] Az adatokat helyesen gyűjti össze és formázza JSON/CSV formátumban
- [ ] A riport könnyen elérhető a szinkronizáció befejezése után
- [ ] Minden komponenshez unit és integrációs tesztek készültek
- [ ] Dokumentáció elkészült

### Additional Notes

- Gondolj a bővíthetőségre, hogy a riport később új mezőkkel bővíthető legyen.
- A riport automatikusan készüljön el minden szinkronizációs folyamat végén.

---

## 3. Párhuzamos feldolgozás bevezetése

### Description

Jelenlegi rendszerünk szekvenciálisan dolgozza fel az adatokat, amely nagyobb adatmennyiségek esetén teljesítménybeli problémákat okozhat. A párhuzamos feldolgozás lehetőségének bevezetésével csökkenthetjük a feldolgozási időt, és hatékonyabban tudjuk kezelni a nagy terhelést is. A feladat célja a párhuzamos feldolgozás architektúrájának kidolgozása, implementálása, és tesztelése, miközben a rendszer stabilitása és megbízhatósága nem sérülhet.

### Tasks

#### 1. Párhuzamos feldolgozás architektúrájának megtervezése 
  - Elemezd a jelenlegi feldolgozási folyamatokat és azonosítsd azokat a részeket, amelyek párhuzamosan futtathatók 
  - Határozd meg a queue-k és workerek számát és típusát a rendszer terhelésének megfelelően

#### 2. Feldolgozási logika módosítása 
  - Implementáld a párhuzamos feldolgozást támogató struktúrát  (pl.: Laravel queue) 
  - Módosítsd a szervizek logikáját, hogy támogassák a queue-alapú feldolgozást

#### 3. Queue és Worker menedzsment beállítása 
  - Hozd létre a szükséges queue konfigurációkat
  - Konfiguráld a worker-eket

#### 4. Dead Letter Queue és retry mechanizmus bevezetése 
  - Gondoskodj róla, hogy a sikertelen feldolgozások esetén a rendszer DLQ-ba helyezze a hibás elemeket
  - Implementáld az automatikus újrapróbálkozási mechanizmust.

#### 5. Teljesítménytesztelés párhuzamos feldolgozás esetén 
  - Teszteld a rendszer teljesítményét - kisebb adatcsomagok, nagyobb adatcsomagok

#### 6. Tesztelés
- Írj unit és integrációs teszteket

#### 7. Dokumentálés
- Dokumentáld az elkészült fejlesztést

### Acceptance criterias

- [ ] A rendszer képes párhuzamosan feldolgozni a bejövő adatokat, és a teljesítmény mérhetően javult
- [ ] A sikertelen feldolgozások megfelelően kerülnek DLQ-ba, és az újrapróbálkozási mechanizmus helyesen működik
- [ ] Minden komponenshez unit és integrációs tesztek készültek
- [ ] Dokumentáció elkészült

### Additional Notes

- Figyelj a race condition helyzetek elkerülésére
- Teljesítmény méréshez használhatsz pl.: Laravel Telescope-ot 

---
