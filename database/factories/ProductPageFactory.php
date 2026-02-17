<?php

namespace Database\Factories;

use App\Models\ProductPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPageFactory extends Factory
{
    protected $model = ProductPage::class;

    public function definition(): array
    {
        return [
            'pin_code' => $this->faker->unique()->bothify('??') . uniqid(),
            'title' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 1000),
            'shipping' => $this->faker->boolean(),
            'shipping_price' => $this->faker->randomFloat(2, 5, 20),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'description' => $this->faker->paragraph(),
            'seller_name' => $this->faker->name(),
            'seller_klaz_user_id' => $this->faker->uuid(),
            'klaz_url' => $this->faker->url(),
            'iban_name' => $this->faker->name(),
            'iban' => $this->faker->iban('DE'),
            'bic' => $this->faker->swiftBicNumber(),
            'fees' => $this->faker->randomFloat(2, 0, 10),
        ];
    }
}
