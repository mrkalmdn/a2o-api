<?php

namespace Database\Factories;

use App\Models\EventName;
use App\Models\Market;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LogEvent>
 */
class LogEventFactory extends Factory
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
            'event_name_id' => EventName::factory(),
            'session_id' => Str::random(10),
            'data' => $this->faker->optional()->text(),
        ];
    }
}
