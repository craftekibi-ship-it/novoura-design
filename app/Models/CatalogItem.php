<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'spec_json' => 'array',
        'fiyat' => 'decimal:2',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
