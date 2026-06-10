<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Supplier extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'cnpj',
        'contact_name',
        'contact_phone',
        'email',
        'address',
        'active'
    ];

    protected $casts = ['active' => 'boolean'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(ProductLot::class);
    }
}

