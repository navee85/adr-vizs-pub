<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['connect_transaction_id', 'announcement', 'is_duplicated', 'synced_at'];

    protected $casts = [
        'synced_at' => 'datetime',
    ];
}
