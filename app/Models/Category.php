<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk slug

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Automatically generate slug before saving
    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) { // Hanya update slug jika nama berubah
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Seorang Category bisa punya banyak Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}