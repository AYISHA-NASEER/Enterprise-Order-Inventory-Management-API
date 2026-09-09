<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();

            $table->string('event_id')->unique();

            $table->string('event_type');

            $table->string('razorpay_payment_id')->nullable();

            $table->string('razorpay_order_id')->nullable();

            $table->json('payload');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};