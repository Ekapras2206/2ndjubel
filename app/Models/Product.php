<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import trait SoftDeletes
use App\Traits\HasLocation; // <--- PASTIKAN BARIS INI ADA DAN BENAR UNTUK TRAIT HASLOCATION
use Illuminate\Support\Facades\Storage; // <--- PASTIKAN BARIS INI ADA UNTUK FACADE STORAGE

class Product extends Model
{
    use HasFactory, SoftDeletes, HasLocation; // Gunakan trait SoftDeletes dan HasLocation

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'condition',
        'city',
        'address',
        'images', // Ini akan di-cast ke array (JSON di DB)
        'status',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * The attributes that should be mutated to dates.
     * Required for SoftDeletes trait.
     *
     * @var array<int, string>
     */
    protected $dates = ['deleted_at']; // Untuk SoftDeletes

    // ------ Relasi Database ------

    /**
     * Get the user (seller) that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the chats for the product.
     */
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Get the transactions for the product.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // ------ Local Scopes (Reusable Query) ------

    /**
     * Scope a query to only include approved products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include products pending verification.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingVerification($query)
    {
        return $query->where('status', 'pending');
    }

    // ------ Custom Methods ------

    /**
     * Check if the product is sold.
     *
     * @return bool
     */
    public function isSold(): bool
    {
        return $this->status === 'sold';
    }

    /**
     * Approve the product.
     *
     * @return void
     */
    public function approve(): void
    {
        $this->status = 'approved';
        $this->published_at = now(); // Set tanggal publikasi saat disetujui
        $this->save();
    }

    /**
     * Reject the product.
     *
     * @return void
     */
    public function reject(): void
    {
        $this->status = 'rejected';
        $this->save();
    }

    /**
     * Get the URL of the first image of the product.
     * Accessor for displaying the main image.
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->images) { // Menggunakan kolom 'images' yang sekarang berisi string path
            return Storage::url($this->images);
        }
        // Mengembalikan URL placeholder jika tidak ada gambar
        return asset('https://placehold.co/600x400/EBF0F5/7F92B0?text=No+Image'); // Ganti path placeholder jika perlu
    }
}
