<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UserGateway extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';
    /**
     * @var string
     */
    protected $collection = 'users';
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'user_type_id',
        'password',
        'img',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
