<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebshopOrder extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'status'];

    protected $casts = [
        'status' => 'string',
    ];
}
