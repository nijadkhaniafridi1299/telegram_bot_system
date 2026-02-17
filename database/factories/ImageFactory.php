<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\ProductPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'product_page_id' => ProductPage::factory(),
            'path' => $this->faker->imageUrl(),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
