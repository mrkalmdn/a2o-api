<?php

namespace Database\Factories;

use App\Models\Market;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LogServiceTitanJob>
 */
class LogServiceTitanJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'market_id' => Market::factory(),
            'start' => now()->startOfDay(),
            'end' => now()->endOfDay(),
        ];
    }
}
