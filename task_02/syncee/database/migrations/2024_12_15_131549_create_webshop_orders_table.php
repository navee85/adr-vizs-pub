<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webshop_orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', OrderStatus::all());
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webshop_orders');
    }
};
