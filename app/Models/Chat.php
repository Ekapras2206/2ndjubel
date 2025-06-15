<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Diperlukan jika Anda menggunakan relasi yang bergantung pada user login

class Chat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'buyer_id',
        'seller_id',
        'product_id',
    ];

    /**
     * Relasi ke User (Pembeli).
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Relasi ke User (Penjual).
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Relasi ke Product yang dibicarakan dalam chat.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relasi ke semua pesan dalam chat.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * ++ RELASI YANG HILANG: Untuk mengambil SATU pesan terakhir. ++
     * Ini adalah relasi HasOne yang diurutkan dari yang terbaru (latest).
     */
    public function latestMessage()
    {
        // hasOne akan otomatis mengambil SATU record.
        // ofMany('id', 'max') adalah cara modern, tapi latest() lebih mudah dipahami.
        return $this->hasOne(ChatMessage::class)->latest();
    }

    /**
     * Metode untuk mendapatkan lawan bicara dari pengguna yang sedang login.
     */
    public function getOtherParticipantAttribute()
    {
        if (!Auth::check()) {
            return null;
        }

        if ($this->buyer_id === Auth::id()) {
            return $this->seller;
        }

        return $this->buyer;
    }
}
