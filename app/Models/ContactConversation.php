<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ContactConversation extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';
    /**
     * @var string
     */
    protected $collection = 'contact_conversations';
    /**
     * @var string[]
     */
    protected $fillable = [
        'chat_id',
        'name',
        'last_time',
        'last_message'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
