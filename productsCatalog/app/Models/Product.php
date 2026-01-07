<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'description',
        'category',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function getFirstImageUrlAttribute(): ?string
    {
        $image = $this->images()
            ->where('url', 'not like', 'data:image%')
            ->first();

        return $image?->url;
    }

    public function scopeWithValidImages($query)
    {
        return $query->whereHas('images', function ($q) {
            $q->where('url', 'not like', 'data:image%');
        });
    }
}
