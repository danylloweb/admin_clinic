<?php

namespace App\Entities;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Class Advert.
 *
 * @package namespace App\Entities;
 */
class AvatarChat extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'avatar_chats';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'avatar',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

}
