<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(50)->create();
        \App\Models\ProductPage::factory(50)->create();
        \App\Models\Image::factory(50)->create();
        \App\Models\PaymentSession::factory(50)->create();
        \App\Models\DeliveryAddress::factory(50)->create();
        \App\Models\VonageAccount::factory(50)->create();
        \App\Models\Proxy::factory(50)->create();
        \App\Models\PaymentMethod::factory(50)->create();
        \App\Models\PaymentWindow::factory(50)->create();
    }
}
