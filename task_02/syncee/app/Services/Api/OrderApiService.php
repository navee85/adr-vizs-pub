<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;

/**
 * Service for handling order-related API operations.
 */
class OrderApiService
{
    protected string $webshopApiUrl;

    /**
     * OrderApiService constructor.
     */
    public function __construct()
    {
        $this->webshopApiUrl = config('services.webshop_api.url');
    }

    /**
     * Fetch orders from webshop service
     *
     * @return array
     * @throws \Throwable
     */
    public function fetchOrders(): array
    {
        return retry(config('services.webshop_api.retry.attempts'), function () {
            $response = Http::get("{$this->webshopApiUrl}/orders/open");

            if ($response->failed()) {
                throw new \Exception('Failed to fetch open orders from the webshop.');
            }

            return $response->json();
        }, config('services.webshop_api.retry.delay'));
    }

    /**
     * Updates the status of an order via the API.
     *
     * @param string $orderId
     * @param string $status
     * @return void
     * @throws \Exception
     */
    public function updateOrderStatus(string $orderId, string $status): void
    {
        retry(
            config('services.webshop_api.retry.attempts'),
            function () use ($orderId, $status) {
                $response = Http::patch("{$this->webshopApiUrl}/orders/{$orderId}", [
                    'status' => $status,
                ]);

                if ($response->failed()) {
                    throw new \Exception("Failed to update status for order ID {$orderId}.");
                }
            },
            config('services.webshop_api.retry.delay')
        );
    }
}
