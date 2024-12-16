<?php

namespace Database\Factories;

use App\Models\ConnectTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectTransactionFactory extends Factory
{
    protected $model = ConnectTransaction::class;

    public function definition(): array
    {
        return [
            'announcement' => $this->faker->numberBetween(1,10),
            'created_at' => now(),
        ];
    }
}
