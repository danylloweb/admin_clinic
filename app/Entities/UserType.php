<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserType.
 *
 * @package namespace App\Entities;
 */
class UserType extends Model implements Transformable
{
    use TransformableTrait;

    const RECEPTION = 1;
    const MEDICAL = 2;
    const MANAGER = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
