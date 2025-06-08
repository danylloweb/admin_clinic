<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Chat extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';
    /**
     * @var string
     */
    protected $collection = 'chats';
    /**
     * @var string[]
     */
    protected $fillable = [
        'chat_id',
        'name',
        'last_time',
        'timestamp',
        'last_message'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
