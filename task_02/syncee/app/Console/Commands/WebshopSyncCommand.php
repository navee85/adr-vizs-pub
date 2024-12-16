<?php

namespace App\Console\Commands;

use App\Contracts\SyncLoggerContract;
use App\Services\Sync\WebshopSyncService as WebshopOrderSyncService;
use Illuminate\Console\Command;

class WebshopSyncCommand extends Command
{
    protected $signature = 'sync:webshop';
    protected $description = 'Synchronize webshop orders.';

    protected WebshopOrderSyncService $service;
    protected SyncLoggerContract $logger;

    public function __construct(WebshopOrderSyncService $service, SyncLoggerContract $logger)
    {
        parent::__construct();
        $this->service = $service;
        $this->logger = $logger;
    }

    public function handle(): void
    {
        $this->logger->info('sync:webshop executed at: ' . now());
        $this->service->sync();
        $this->info('Webshop synchronized successfully.');
    }
}
