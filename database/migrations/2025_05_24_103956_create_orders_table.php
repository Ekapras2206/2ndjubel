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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Bisa null jika guest checkout
            $table->decimal('total_amount', 10, 2);
            $table->string('shipping_address');
            $table->string('phone_number', 20);
            $table->string('payment_method'); // e.g., 'bank_transfer', 'cod'
            $table->string('status')->default('pending'); // e.g., 'pending', 'processing', 'completed', 'cancelled'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};