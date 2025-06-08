<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Advert.
 *
 * @package namespace App\Entities;
 */
class Advert extends Model implements Transformable
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
        'code',
        'url_site',
        'url_checkout',
        'status',
        'qty_click_confirmed',
        'qty_click_checkout',
        'qty_convert',
        'price_per_click',
        'message_to_lead',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

}
