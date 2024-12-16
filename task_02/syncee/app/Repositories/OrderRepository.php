<?php

namespace App\Repositories;

use App\Models\Order;
use App\Enums\OrderStatus;

/**
 * Repository for managing Order model operations.
 */
class OrderRepository
{
    /**
     * Save orders to DB
     *
     * @param array $order
     * @return void
     */
    public function saveOrder(array $order): void
    {
        Order::create([
            'webshop_order_id' => $order['id'],
            'status' => $order['status'],
        ]);
    }

    /**
     * Find an order by its webshop_order_id.
     *
     * @param int $webshopOrderId
     * @return Order|null
     */
    public function findByWebshopOrderId(int $webshopOrderId): ?Order
    {
        return Order::where('webshop_order_id', $webshopOrderId)->first();
    }

    /**
     * Marks an order as synced.
     *
     * @param Order $order
     * @param \DateTime|string $timestamp
     * @return void
     */
    public function markAsSynced(Order $order, $timestamp): void
    {
        $order->update([
            'status' => OrderStatus::PAID->value,
            'synced_at' => $timestamp,
        ]);
    }
}
