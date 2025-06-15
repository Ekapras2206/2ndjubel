<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'shipping_address',
        'phone_number',
        'payment_method',
        'status',
    ];

    // Relasi dengan User (jika pesanan dilakukan oleh user terdaftar)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan OrderItems (satu pesanan memiliki banyak item pesanan)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}