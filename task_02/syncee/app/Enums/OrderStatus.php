<?php

namespace App\Enums;

enum OrderStatus: string
{
    case OPEN = 'open';
    case PAID = 'paid';

    /**
     * Get all statuses as an array.
     *
     * @return array
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the given status is valid.
     *
     * @param string $status
     * @return bool
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::all(), true);
    }
}
