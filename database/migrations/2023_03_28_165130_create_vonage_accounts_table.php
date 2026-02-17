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
        Schema::create('vonage_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('display_name', 50)->nullable(false)->unique();
            $table->string('api_key', 2000)->nullable(false);
            $table->string('api_secret', 200)->nullable(false);
            $table->string('remaining_balance', 50)->nullable();
            $table->integer('sms_sent')->nullable(false)->default(0);
            $table->integer('sms_failures')->nullable(false)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vonage_accounts');
    }
};
