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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke produk yang terlibat
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            // Foreign Key ke penjual
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            // Foreign Key ke pembeli
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('final_price', 10, 2); // Harga final transaksi (bisa beda dari harga produk awal)
            // Status transaksi: pending (menunggu pembayaran/konfirmasi), completed, canceled, refunded
            $table->enum('status', ['pending', 'completed', 'canceled', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // Metode pembayaran (opsional)
            $table->timestamp('transaction_date'); // Tanggal dan waktu transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};