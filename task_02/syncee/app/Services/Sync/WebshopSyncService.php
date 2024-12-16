<?php

namespace App\Services\Sync;

use App\Data\WebshopOrderData;
use App\Contracts\SyncLoggerContract;
use App\Repositories\OrderRepository;
use App\Services\Api\OrderApiService;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Handles the synchronization of orders from the Webshop API.
 */
class WebshopSyncService
{
    protected OrderApiService $orderApiService;
    protected OrderRepository $orderRepository;
    protected SyncLoggerContract $logger;

    /**
     * Webshop constructor.
     *
     * @param OrderApiService $orderApiService
     * @param OrderRepository $orderRepository
     * @param SyncLoggerContract $logger
     */
    public function __construct(
        OrderApiService $orderApiService,
        OrderRepository $orderRepository,
        SyncLoggerContract $logger
    ) {
        $this->orderApiService = $orderApiService;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * Synchronizes orders from the Webshop API to the database.
     *
     * @return void
     */
    public function sync(): void
    {
        try {
            $orders = $this->orderApiService->fetchOrders();

            foreach ($orders as $order) {
                $this->processOrder($order);
            }
        } catch (Throwable $e) {
            $this->logger->critical('Webshop orders synchronization failed.', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Processes a single order.
     *
     * @param array $order
     * @return void
     */
    protected function processOrder(array $order): void
    {
        try {
            $validatedOrder = WebshopOrderData::validateAndCreate($order);
            $this->orderRepository->saveOrder($validatedOrder->toArray());
        } catch (ValidationException $validationException) {
            $this->logger->error('Validation failed for order.', [
                'data' => $order,
                'errors' => $validationException->errors(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to save order.', [
                'order_id' => $order['id'] ?? null,
                'status' => $order['status'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
