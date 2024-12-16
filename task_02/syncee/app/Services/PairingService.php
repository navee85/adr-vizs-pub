<?php

namespace App\Services;

use App\Contracts\SyncLoggerContract;
use App\Enums\OrderStatus;
use App\Models\Transaction;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Services\Api\OrderApiService;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Handles pairing of orders and transactions.
 */
class PairingService
{
    protected OrderRepository $orderRepository;
    protected TransactionRepository $transactionRepository;
    protected OrderApiService $orderApiService;
    protected SyncLoggerContract $logger;

    /**
     * Pairing constructor.
     *
     * @param OrderRepository $orderRepository
     * @param TransactionRepository $transactionRepository
     * @param OrderApiService $orderApiService
     */
    public function __construct(
        OrderRepository $orderRepository,
        TransactionRepository $transactionRepository,
        OrderApiService $orderApiService,
        SyncLoggerContract $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->transactionRepository = $transactionRepository;
        $this->orderApiService = $orderApiService;
        $this->logger = $logger;
    }

    /**
     * Executes the pairing process.
     *
     * @return void
     */
    public function execute(): void
    {
        $transactions = $this->transactionRepository->getUnsyncedTransactions();

        foreach ($transactions as $transaction) {
            $this->processTransaction($transaction);
        }
    }

    /**
     * Processes a single transaction and pairs it with an order.
     *
     * @param Transaction $transaction
     * @return void
     */
    protected function processTransaction(Transaction $transaction): void
    {
        $order = $this->orderRepository->findByWebshopOrderId($transaction->announcement);

        if (!$order) {
            $this->logger->info('Transaction does not match any order.', [
                'transaction_id' => $transaction->id,
                'connect_transaction_id' => $transaction->connect_transaction_id,
            ]);
            return;
        }

        if ($order->status->value === OrderStatus::PAID->value) {
            $this->transactionRepository->markAsDuplicated($transaction);
            $this->logDuplicatedTransaction($transaction);
            return;
        }

        DB::beginTransaction();

        try {
            $this->orderApiService->updateOrderStatus($order->id, OrderStatus::PAID->value);

            $this->orderRepository->markAsSynced($order, now());
            $this->transactionRepository->markAsSynced($transaction, now());

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->logger->error('Failed to process transaction.', [
                'transaction_id' => $transaction->id,
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Logs a duplicated transaction.
     *
     * @param Transaction $transaction
     * @return void
     */
    private function logDuplicatedTransaction(Transaction $transaction): void
    {
        $this->logger->warning('Duplicated transaction detected.', [
            'transaction_id' => $transaction->id,
            'connect_transaction_id' => $transaction->connect_transaction_id,
        ]);
    }
}
