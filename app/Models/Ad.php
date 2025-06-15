<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Untuk URL gambar

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_path',
        'link',
        'type',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ------ Relasi Database ------

    // Sebuah Ad bisa dimiliki oleh satu User (pemilik iklan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ------ Custom Methods ------

    // Mengecek apakah iklan sedang aktif saat ini
    public function isActiveNow(): bool
    {
        return $this->is_active &&
               $this->start_date->lte(now()) && // Start date kurang dari atau sama dengan sekarang
               $this->end_date->gte(now());    // End date lebih dari atau sama dengan sekarang
    }

    // Mendapatkan URL gambar iklan
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}