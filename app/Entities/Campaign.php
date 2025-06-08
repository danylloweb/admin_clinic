<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Campaign.
 *
 * @package namespace App\Entities;
 */
class Campaign extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'url_image',
        'date',
        'status',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
    ];

}

