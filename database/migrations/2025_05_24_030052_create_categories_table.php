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
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Kolom ID auto-incrementing primary key (unsigned big integer)
            $table->string('name')->unique(); // Nama kategori, harus unik (tidak boleh ada kategori dengan nama yang sama)
            $table->string('slug')->unique(); // Slug untuk URL, juga harus unik
            $table->text('description')->nullable(); // Deskripsi kategori, boleh kosong
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at secara otomatis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories'); // Saat rollback, hapus tabel categories jika ada
    }
};