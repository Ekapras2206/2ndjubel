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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade'); // Rating untuk item pesanan tertentu
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pengguna yang memberi rating (pembeli)
            $table->unsignedTinyInteger('score'); // Skor rating, misal 1-5
            $table->text('comment')->nullable(); // Komentar rating
            $table->timestamps();

            // Memastikan satu rating per item pesanan per pengguna
            $table->unique(['order_item_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};