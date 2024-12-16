<?php

namespace App\Services\Logging;

use App\Contracts\SyncLoggerContract;
use Illuminate\Support\Facades\Log;

class SyncLogger implements SyncLoggerContract
{
    const CHANNEL = 'sync';

    /**
     * Log an info message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        Log::channel(self::CHANNEL)->info($message, $context);
    }

    /**
     * Log a waring message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        Log::channel(self::CHANNEL)->warning($message, $context);
    }

    /**
     * Log an error message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        Log::channel(self::CHANNEL)->error($message, $context);
    }

    /**
     * Log a critical message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        Log::channel(self::CHANNEL)->critical($message, $context);
    }
}
