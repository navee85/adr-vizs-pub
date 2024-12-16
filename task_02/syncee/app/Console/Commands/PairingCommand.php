<?php

namespace App\Console\Commands;

use App\Contracts\SyncLoggerContract;
use App\Services\PairingService as PairingService;
use Illuminate\Console\Command;

class PairingCommand extends Command
{
    protected $signature = 'sync:pairing';
    protected $description = 'Pairing orders with transactions.';

    protected PairingService $service;
    protected SyncLoggerContract $logger;

    public function __construct(PairingService $service, SyncLoggerContract $logger)
    {
        parent::__construct();
        $this->service = $service;
        $this->logger = $logger;
    }

    public function handle(): void
    {
        $this->logger->info('sync:pairing executed at: ' . now());
        $this->service->execute();
        $this->info('Pairing completed successfully.');
    }
}
