<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Opsional: jika transaksi bisa di-soft delete

class Transaction extends Model
{
    use HasFactory; // Jika ingin pakai soft deletes, tambahkan ", SoftDeletes;"

    protected $fillable = [
        'product_id',
        'seller_id',
        'buyer_id',
        'final_price',
        'status',
        'payment_method',
        'transaction_date',
    ];

    protected $casts = [
        'final_price' => 'decimal:2', // Casting harga ke desimal dengan 2 angka di belakang koma
        'transaction_date' => 'datetime', // Otomatis jadi Carbon instance
    ];

    // protected $dates = ['deleted_at']; // Jika menggunakan soft deletes

    // ------ Relasi Database ------

    // Sebuah Transaction terkait dengan satu Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Sebuah Transaction memiliki satu Seller
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Sebuah Transaction memiliki satu Buyer
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // Sebuah Transaction bisa punya satu Rating
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    // Mengecek apakah transaksi sudah selesai
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    // Mengecek apakah transaksi bisa diberi rating oleh user tertentu
    public function canBeRatedBy(User $user): bool
    {
        return $this->isCompleted() &&
               ($user->id === $this->buyer_id || $user->id === $this->seller_id) &&
               !$this->rating()->exists(); // Belum ada rating untuk transaksi ini
    }
}