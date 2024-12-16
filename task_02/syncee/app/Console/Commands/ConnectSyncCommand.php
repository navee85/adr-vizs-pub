<?php

namespace App\Console\Commands;

use App\Contracts\SyncLoggerContract;
use App\Services\Sync\ConnectSyncService as ConnectSyncService;
use Illuminate\Console\Command;

class ConnectSyncCommand extends Command
{
    protected $signature = 'sync:connect';
    protected $description = 'Synchronize connect transactions.';

    protected ConnectSyncService $service;
    protected SyncLoggerContract $logger;

    public function __construct(ConnectSyncService $service, SyncLoggerContract $logger)
    {
        parent::__construct();
        $this->service = $service;
        $this->logger = $logger;
    }

    public function handle(): void
    {
        $this->logger->info('sync:connect executed at: ' . now());
        $this->service->sync();
        $this->info('Connect synchronized successfully.');
    }
}
