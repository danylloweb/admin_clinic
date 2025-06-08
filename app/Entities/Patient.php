<?php

namespace App\Entities;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Patient.
 *
 * @package namespace App\Entities;
 */
class Patient extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'birth_date',
        'sex',
        'social_name',
        'chat_id',
    ];
    /**
     * @var array
     */
    protected $dates = [
        'birth_date',
        'created_at',
        'updated_at',
    ];

    public function getLastMessageByChatId()
    {
        $chat = Chat::where('chat_id',$this->attributes['chat_id'])->first();
        return $chat->last_message??"";
    }

}
