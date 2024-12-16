<?php

namespace App\Repositories;

use App\Models\Transaction;

/**
 * Repository for managing Transaction model operations.
 */
class TransactionRepository
{
    /**
     * Saves a transaction to the database.
     *
     * @param array $transaction
     * @return void
     */
    public function saveTransaction(array $transaction): void
    {
        Transaction::create([
            'connect_transaction_id' => $transaction['id'],
            'announcement' => $transaction['announcement'],
        ]);
    }

    /**
     * Retrieves all unsynced transactions that are not marked as duplicated.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUnsyncedTransactions()
    {
        return Transaction::whereNull('synced_at')
            ->where('is_duplicated', false)
            ->get();
    }

    /**
     * Marks a transaction as synced.
     *
     * @param Transaction $transaction
     * @param \DateTime|string $timestamp
     * @return void
     */
    public function markAsSynced(Transaction $transaction, $timestamp): void
    {
        $transaction->update([
            'synced_at' => $timestamp,
        ]);
    }

    /**
     * Marks a transaction as duplicated.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function markAsDuplicated(Transaction $transaction): void
    {
        $transaction->update([
            'is_duplicated' => true,
        ]);
    }
}
