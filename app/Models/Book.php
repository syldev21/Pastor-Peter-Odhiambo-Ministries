<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'category_id',
        'price',
        'description',
        'stock',
        'is_devotional',
        'is_featured',
        'cover_image',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
    public function isLowStock(): bool
    {
        return $this->stock <= 5; // You can adjust threshold
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock', '<=', 5);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}