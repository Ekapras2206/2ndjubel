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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke produk yang sedang dibahas
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            // Foreign Key ke penjual
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            // Foreign Key ke pembeli
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            // Memastikan hanya ada satu sesi chat per produk dan pembeli
            $table->unique(['product_id', 'buyer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};