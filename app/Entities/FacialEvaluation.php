<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FacialEvaluation.
 *
 * @package namespace App\Entities;
 */
class FacialEvaluation extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $table = 'facial_evaluations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'professional_id',
        'chief_complaint',
        'skin_type',
        'oiliness',
        'hydration',
        'sensitivity',
        'acne',
        'acne_notes',
        'melasma',
        'melasma_notes',
        'wrinkles',
        'wrinkles_notes',
        'flaccidity',
        'flaccidity_notes',
        'spots',
        'spots_notes',
        'dilated_pores',
        'dilated_pores_notes',
        'fitzpatrick_type',
        'aesthetic_history',
        'allergies',
        'medications_in_use',
        'patient_objective',
        'treatment_plan',
        'photo_front',
        'photo_profile_right',
        'photo_profile_left',
        'consent_accepted',
        'patient_signature',
        'professional_signature',
        'signature_token',
        'signature_token_expires_at',
        'signed_at',
    ];

    protected $casts = [
        'aesthetic_history'          => 'array',
        'treatment_plan'             => 'array',
        'acne'                       => 'boolean',
        'melasma'                    => 'boolean',
        'wrinkles'                   => 'boolean',
        'flaccidity'                 => 'boolean',
        'spots'                      => 'boolean',
        'dilated_pores'              => 'boolean',
        'consent_accepted'           => 'boolean',
        'signature_token_expires_at' => 'datetime',
        'signed_at'                  => 'datetime',
    ];


    public function getChatAttributes(): array
    {
        $patient = Patient::find($this->attributes['patient_id']);
        return [
            'id'      => $patient->id,
            'social_name' => $patient->social_name ?: $patient->name,
            'chat_id' => $patient->chat_id,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id', 'id');
    }

    public function isTokenValid(): bool
    {
        if (empty($this->signature_token) || empty($this->signature_token_expires_at)) {
            return false;
        }

        return Carbon::parse($this->signature_token_expires_at)->isFuture();
    }

    public function isSigned(): bool
    {
        return !empty($this->signed_at) && (bool) $this->consent_accepted;
    }

}
