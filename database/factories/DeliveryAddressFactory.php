<?php

namespace Database\Factories;

use App\Models\DeliveryAddress;
use App\Models\PaymentSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryAddressFactory extends Factory
{
    protected $model = DeliveryAddress::class;

    public function definition(): array
    {
        return [
            'payment_session_id' => PaymentSession::factory(),
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'address' => $this->faker->streetAddress(),
            'zip' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'email' => $this->faker->safeEmail(),
        ];
    }
}
