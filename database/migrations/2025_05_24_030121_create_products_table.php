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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel users (penjual)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Jika user dihapus, produknya juga dihapus
            // Foreign Key ke tabel categories
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict'); // Mencegah hapus kategori jika ada produk yang menggunakannya
            $table->string('title'); // Judul produk
            $table->text('description'); // Deskripsi produk (teks panjang)
            $table->decimal('price', 10, 2); // Harga (total 10 digit, 2 di belakang koma)
            $table->string('condition'); // Kondisi barang (misal: 'baru', 'bekas_baik', 'bekas_rusak')
            $table->string('city'); // Kota lokasi barang
            $table->string('address')->nullable(); // Alamat detail (opsional)
            $table->double('latitude'); // Latitude untuk koordinat lokasi
            $table->double('longitude'); // Longitude untuk koordinat lokasi
            $table->json('images')->nullable(); // Path gambar, disimpan sebagai JSON array (misal: ['path/img1.jpg', 'path/img2.jpg'])
            // Status produk: pending (menunggu verifikasi), approved, rejected, sold, unavailable
            $table->enum('status', ['pending', 'approved', 'rejected', 'sold', 'unavailable'])->default('pending');
            $table->timestamp('published_at')->nullable(); // Waktu produk disetujui/dipublikasikan
            $table->timestamps();
            $table->softDeletes(); // Menambahkan kolom 'deleted_at' untuk soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};