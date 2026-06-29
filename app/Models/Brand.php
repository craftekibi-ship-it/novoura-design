<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $guarded = [];

    protected $casts = [
        'renkler' => 'array',
        'fontlar' => 'array',
    ];

    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
