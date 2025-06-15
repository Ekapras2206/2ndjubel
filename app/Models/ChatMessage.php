<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ------ Relasi Database ------

    // Sebuah ChatMessage terkait dengan satu Chat
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    // Sebuah ChatMessage dikirim oleh satu User (sender)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Mengecek apakah pesan ini dikirim oleh user yang sedang login
    public function isSentByCurrentUser(): bool
    {
        return auth()->check() && $this->sender_id === auth()->id();
    }
}