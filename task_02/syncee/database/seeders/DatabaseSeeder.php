<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\WebshopOrder;
use App\Models\ConnectTransaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createWebshopOrders();
        $this->createConnectTransactions();
    }

    private function createWebshopOrders()
    {
        WebshopOrder::factory()->count(3)->state([
            'status' => OrderStatus::OPEN->value,
        ])->create();

        WebshopOrder::factory()->count(2)->state([
            'status' => OrderStatus::PAID->value,
        ])->create();
    }

    private function createConnectTransactions()
    {
        $announcements = [1, 3, 3, 4, -1];
        foreach ($announcements as $announcement) {
            ConnectTransaction::factory()->create([
                'announcement' => $announcement,
            ]);
        }
    }
}
