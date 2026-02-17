<?php

namespace Database\Factories;

use App\Models\PaymentSession;
use App\Models\ProductPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentSessionFactory extends Factory
{
    protected $model = PaymentSession::class;

    public function definition(): array
    {
        return [
            'product_page_id' => ProductPage::factory(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
