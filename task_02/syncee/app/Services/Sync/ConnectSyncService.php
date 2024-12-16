<?php

namespace App\Services\Sync;

use App\Contracts\SyncLoggerContract;
use App\Data\ConnectTransactionData;
use App\Repositories\TransactionRepository;
use App\Services\Api\ConnectApiService;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Handles the synchronization of transactions from the Connect API.
 */
class ConnectSyncService
{
    protected ConnectApiService $connectApiService;
    protected TransactionRepository $transactionRepository;
    protected SyncLoggerContract $logger;

    /**
     * Connect constructor.
     *
     * @param ConnectApiService $connectApiService
     * @param TransactionRepository $transactionRepository
     * @param SyncLoggerContract $logger
     */
    public function __construct(
        ConnectApiService $connectApiService,
        TransactionRepository $transactionRepository,
        SyncLoggerContract $logger
    ) {
        $this->connectApiService = $connectApiService;
        $this->transactionRepository = $transactionRepository;
        $this->logger = $logger;
    }

    /**
     * Synchronizes transactions from the Connect API to the database.
     *
     * @return void
     */
    public function sync(): void
    {
        try {
            $transactions = $this->connectApiService->fetchTransactions();

            foreach ($transactions as $transaction) {
                $this->processTransaction($transaction);
            }
        } catch (Throwable $e) {
            $this->logger->critical('Connect transactions synchronization failed.', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Processes a single transaction.
     *
     * @param array $transaction
     * @return void
     */
    protected function processTransaction(array $transaction): void
    {
        try {
            $validatedTransaction = ConnectTransactionData::validateAndCreate($transaction);

            $this->transactionRepository->saveTransaction($validatedTransaction->toArray());
        } catch (ValidationException $validationException) {
            $this->logger->error('Validation failed for transaction.', [
                'data' => $transaction,
                'errors' => $validationException->errors(),
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Failed to save transaction.', [
                'transaction_id' => $transaction['id'] ?? null,
                'announcement' => $transaction['announcement'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
