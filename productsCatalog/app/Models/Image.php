<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'url',
    ];

    protected $casts = [
        'product_id' => 'integer',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

        public function isValid(): bool
    {
        return !str_starts_with($this->url, 'data:image');
    }

    public function scopeWithValidImages($query)
    {
        return $query->whereHas('images', function ($q) {
            $q->where('url', 'not like', 'data:image%');
        });
    }

    public function getFirstImageUrlAttribute(): string
    {
        $image = $this->images->first();
        $imageUrl = $image?->url;

        if (!$imageUrl || 
            str_starts_with($imageUrl, 'data:image') || 
            !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return 'data:image/svg+xml,' . rawurlencode('
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                    <rect width="100" height="100" fill="#e5e7eb"/>
                    <g transform="translate(50,50)">
                        <path d="M-15,-10 L-15,10 L15,10 L15,-10 Z M-10,-15 L10,-15 L10,-5 L-10,-5 Z M-5,15 L5,15 L5,20 L-5,20 Z" fill="#9ca3af"/>
                        <circle cx="0" cy="-5" r="3" fill="#6b7280"/>
                    </g>
                </svg>
            ');
        }

        return $imageUrl;
    }
}
