<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;

/**
 * Service for interacting with the Connect API.
 */
class ConnectApiService
{
    protected string $connectApiUrl;

    /**
     * ConnectApiService constructor.
     */
    public function __construct()
    {
        $this->connectApiUrl = config('services.connect_api.url');
    }

    /**
     * Fetch transactions from the Connect API.
     *
     * @return array
     * @throws \Exception
     */
    public function fetchTransactions(): array
    {
        return retry(
            config('services.connect_api.retry.attempts'),
            function () {
                $response = Http::get("{$this->connectApiUrl}/transactions");

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch transactions from the Connect API.');
                }

                return $response->json();
            },
            config('services.connect_api.retry.delay')
        );
    }
}
