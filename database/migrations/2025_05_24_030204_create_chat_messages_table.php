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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke sesi chat
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            // Foreign Key ke pengirim pesan (bisa seller atau buyer)
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message'); // Isi pesan
            $table->boolean('is_read')->default(false); // Status sudah dibaca atau belum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};