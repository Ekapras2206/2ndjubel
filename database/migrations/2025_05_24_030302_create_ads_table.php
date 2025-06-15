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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke user pemilik iklan (jika ada), bisa null jika iklan dari admin internal
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title'); // Judul iklan
            $table->text('content'); // Konten iklan (bisa teks, HTML)
            $table->string('image_path')->nullable(); // Path gambar iklan, boleh kosong
            $table->string('link')->nullable(); // Link tujuan iklan, boleh kosong
            // Tipe iklan: banner, featured_product (produk unggulan), homepage_promo
            $table->enum('type', ['banner', 'featured_product', 'homepage_promo'])->default('banner');
            $table->timestamp('start_date'); // Tanggal mulai tayang
            $table->timestamp('end_date'); // Tanggal selesai tayang
            $table->boolean('is_active')->default(true); // Status aktif/tidak aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};