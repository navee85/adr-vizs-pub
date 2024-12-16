<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;

class ConnectTransactionData extends Data
{
    /**
     * @param int $id
     * @param int $announcement
     */
    public function __construct(
        public int $id,
        #[IntegerType, Min(1)]
        public int $announcement,
    ) {
    }
}
