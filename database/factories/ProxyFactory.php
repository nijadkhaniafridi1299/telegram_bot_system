<?php

namespace Database\Factories;

use App\Models\Proxy;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProxyFactory extends Factory
{
    protected $model = Proxy::class;

    public function definition(): array
    {
        return [
            'enabled' => $this->faker->boolean(),
            'host' => $this->faker->ipv4(),
            'port' => $this->faker->numberBetween(1000, 9999),
            'user' => $this->faker->userName(),
            'password' => $this->faker->password(),
        ];
    }
}
