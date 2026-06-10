<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDocument extends Model
{
    protected $fillable = ['product_id', 'document_type', 'url', 'original_filename', 'mime_type', 'file_size', 'description'];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

