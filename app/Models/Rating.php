<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'user_id',
        'score',
        'comment',
    ];

    // Relasi ke OrderItem yang diberi rating
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Relasi ke User yang memberi rating
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}