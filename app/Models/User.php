<?php

namespace App\Models;

// Import trait untuk soft deletes
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes; // Tambahkan SoftDeletes

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // Tambahkan ini agar bisa diisi secara massal saat registrasi atau update
        'phone_number', // Tambahkan
        'bio',          // Tambahkan
        'profile_picture', // Tambahkan
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean', // Penting: memastikan 'is_admin' selalu di-cast ke boolean
    ];

    // ------ Relasi Database ------

    // Seorang User bisa punya banyak Products (sebagai penjual)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Seorang User bisa punya banyak Chat (sebagai pembeli atau penjual)
    public function chatsAsSeller()
    {
        return $this->hasMany(Chat::class, 'seller_id');
    }

    public function chatsAsBuyer()
    {
        return $this->hasMany(Chat::class, 'buyer_id');
    }

    // Seorang User bisa punya banyak Transactions (sebagai pembeli)
    public function transactionsAsBuyer()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    // Seorang User bisa punya banyak Transactions (sebagai penjual)
    public function transactionsAsSeller()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    // Seorang User bisa memberikan banyak Ratings
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    // Seorang User bisa menerima banyak Ratings
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'rated_id');
    }

    // Seorang User bisa punya banyak Iklan
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    // ------ Custom Methods ------

    // Mengecek apakah user adalah admin
// ------ Custom Methods ------

// Mengecek apakah user adalah admin
public function isAdmin(): bool
{
    // return $this->role === 'admin'; // Old line
    return $this->is_admin;          // New line: directly return the boolean value
}

    // Mendapatkan rating rata-rata yang diterima user
    public function getAverageRatingAttribute(): float
    {
        return $this->receivedRatings()->avg('score') ?? 0.0;
    }

    // Mendapatkan URL gambar profil
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile_picture ? Storage::url($this->profile_picture) : 'https://via.placeholder.com/150'; // Placeholder jika tidak ada gambar
    }
}