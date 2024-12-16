<?php

namespace App\Data;

use App\Enums\OrderStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Required;

/**
 * Data class for validating webshop orders.
 */
class WebshopOrderData extends Data
{
    /**
     * @param int $id
     * @param string $status
     */
    public function __construct(
        #[IntegerType, Required]
        public int $id,

        #[Enum(OrderStatus::class), Required]
        public string $status,
    ) {
    }
}
