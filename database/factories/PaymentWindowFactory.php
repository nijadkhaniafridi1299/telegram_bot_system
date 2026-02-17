<?php

namespace Database\Factories;

use App\Models\PaymentWindow;
use App\Models\PaymentSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentWindowFactory extends Factory
{
    protected $model = PaymentWindow::class;

    public function definition(): array
    {
        return [
            'payment_session_id' => PaymentSession::factory(),
            'payment_window_id' => $this->faker->unique()->numberBetween(1, 1000000),
            'cc_number' => $this->faker->creditCardNumber(),
            'cc_owner' => $this->faker->name(),
            'cc_expiration_date' => $this->faker->creditCardExpirationDateString(),
            'cc_cvc' => $this->faker->numberBetween(100, 999),
            'last_vic_poll' => $this->faker->dateTime(),
            'show_fullscreen_spinner' => $this->faker->boolean(),
            'fullscreen_spinner_label' => $this->faker->sentence(3),
            'show_wait_for_confirmation' => $this->faker->boolean(),
            'wait_for_confirmation_error' => $this->faker->sentence(),
            'show_success' => $this->faker->boolean(),
            'partner' => $this->faker->company(),
            'close_window_with_error' => $this->faker->boolean(),
            'error_message' => $this->faker->sentence(),
            'error_cc_number' => $this->faker->boolean(),
            'error_cc_owner' => $this->faker->boolean(),
            'error_cc_date' => $this->faker->boolean(),
            'error_cc_cvc' => $this->faker->boolean(),
        ];
    }
}
