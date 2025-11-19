<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // e.g., pending, paid, shipped
            $table->string('payment_ref')->nullable();    // M-Pesa or PayPal reference

            // ✅ Delivery fields added directly
            $table->string('delivery_name');
            $table->string('delivery_phone');
            $table->string('delivery_address');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
