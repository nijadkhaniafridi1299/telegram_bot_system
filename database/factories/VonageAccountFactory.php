<?php

namespace Database\Factories;

use App\Models\VonageAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class VonageAccountFactory extends Factory
{
    protected $model = VonageAccount::class;

    public function definition(): array
    {
        return [
            'display_name' => substr($this->faker->unique()->company() . ' ' . uniqid(), 0, 50),
            'api_key' => $this->faker->uuid(),
            'api_secret' => $this->faker->password(),
            'remaining_balance' => $this->faker->randomFloat(2, 0, 100),
            'sms_sent' => $this->faker->numberBetween(0, 1000),
            'sms_failures' => $this->faker->numberBetween(0, 50),
        ];
    }
}
