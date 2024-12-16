<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\WebshopOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebshopOrderFactory extends Factory
{
    protected $model = WebshopOrder::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(OrderStatus::all()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
