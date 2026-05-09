<?php

namespace App\Entities;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(PatientMedicalRecord::class, 'patient_id', 'id');
    }

    public function facialEvaluations()
    {
        return $this->hasMany(FacialEvaluation::class, 'patient_id', 'id');
    }

    public function latestFacialEvaluation()
    {
        return $this->hasOne(FacialEvaluation::class, 'patient_id', 'id')->latestOfMany();
    }

}
