<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connect_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('announcement');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connect_transactions');
    }
};
