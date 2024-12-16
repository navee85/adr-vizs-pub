<?php

namespace App\Http\Controllers;

use App\Models\ConnectTransaction;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\WebshopOrder;
use App\Services\PairingService as PairingService;
use App\Services\Sync\ConnectSyncService as ConnectSyncService;
use App\Services\Sync\WebshopSyncService as WebshopSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * [!] Sync processes controller - only for manual testing [!]
 */
class SyncController extends Controller
{
    /**
     * Sync orders from Webshop
     *
     * @param WebshopSyncService $webshopSyncService
     * @return JsonResponse
     * @throws \Throwable
     */
    public function syncWebshop(WebshopSyncService $webshopSyncService): JsonResponse
    {
        try {
            $webshopSyncService->sync();
        } catch (\Exception $ex) {
            return response()->json(['status' => 'Sync Webshop ERROR - ' . $ex->getMessage()], Response::HTTP_OK);
        }

        return response()->json(['status' => 'Sync Webshop OK'], Response::HTTP_OK);
    }

    /**
     * Sync transactions from Connect
     *
     * @param ConnectSyncService $connectSyncService
     * @return JsonResponse
     */
    public function syncConnect(ConnectSyncService $connectSyncService): JsonResponse
    {
        try {
            $connectSyncService->sync();
        } catch (\Exception $ex) {
            return response()->json(['status' => 'Sync Connect ERROR - ' . $ex->getMessage()], Response::HTTP_OK);
        }

        return response()->json(['status' => 'Sync Connect OK'], Response::HTTP_OK);
    }

    /**
     * Call order and transaction sync
     *
     * @param PairingService $pairingService
     * @return JsonResponse
     */
    public function sync(PairingService $pairingService): JsonResponse
    {
        try {
            $pairingService->execute();
        } catch (\Exception $ex) {
            return response()->json(['status' => 'Sync ERROR - ' . $ex->getMessage()], Response::HTTP_OK);
        }

        return response()->json(['status' => 'Sync OK'], Response::HTTP_OK);
    }

    /**
     * List all data table full content
     *
     * @return JsonResponse
     */
    public function syncStatus(): JsonResponse
    {
        return response()->json([
            'orders' => Order::all(),
            'transactions' => Transaction::all(),
            'webshop_orders' => WebshopOrder::all(),
            'connect_transactions' => ConnectTransaction::all()
        ], Response::HTTP_OK);
    }
}
