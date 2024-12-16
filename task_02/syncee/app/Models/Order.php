<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['webshop_order_id', 'status', 'synced_at'];

    protected $casts = [
        'status' => OrderStatus::class,
        'synced_at' => 'datetime',
    ];
}
