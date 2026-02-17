<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use App\Models\PaymentSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'payment_session_id' => PaymentSession::factory(),
            'method' => $this->faker->randomElement(['cc', 'paypal', 'sofort']),
        ];
    }
}
