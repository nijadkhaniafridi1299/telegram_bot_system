<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_pages', function (Blueprint $table) {
            $table->id();
            $table->string('pin_code', 50)->nullable(false)->unique();
            $table->string('title', 300)->nullable(false);
            $table->integer('price')->nullable(false);
            $table->boolean('shipping')->nullable(false);
            $table->double('shipping_price')->nullable();
            $table->string('postal_code', 15)->nullable(false);
            $table->string('city', 50)->nullable(false);
            $table->string('description', 5000)->nullable();
            $table->string('seller_name', 100)->nullable(false);
            $table->string('seller_klaz_user_id', 100)->nullable();
            $table->string('klaz_url', 1000)->nullable();
            $table->string('iban_name', 100)->nullable(false);
            $table->string('iban', 50)->nullable(false);
            $table->string('bic', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pages');
    }
};
