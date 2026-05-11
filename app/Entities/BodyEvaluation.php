<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BodyEvaluation.
 *
 * @package namespace App\Entities;
 */
class BodyEvaluation extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $table = 'body_evaluations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'professional_id',
        'weight',
        'height',
        'fat_percentage',
        'muscle_mass',
        'objectives',
        'perimetry',
        'cellulite',
        'flaccidity',
        'liquid_retention',
        'body_map_areas',
        'medical_history',
        'previous_procedures',
        'treatment_plan',
        'evolution_sessions',
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
        'weight'                     => 'float',
        'height'                     => 'float',
        'fat_percentage'             => 'float',
        'muscle_mass'                => 'float',
        'objectives'                 => 'array',
        'perimetry'                  => 'array',
        'cellulite'                  => 'array',
        'flaccidity'                 => 'array',
        'liquid_retention'           => 'boolean',
        'body_map_areas'             => 'array',
        'medical_history'            => 'array',
        'treatment_plan'             => 'array',
        'evolution_sessions'         => 'array',
        'consent_accepted'           => 'boolean',
        'signature_token_expires_at' => 'datetime',
        'signed_at'                  => 'datetime',
    ];

    public function getChatAttributes(): array
    {
        $patient = Patient::find($this->attributes['patient_id']);
        return [
            'id'           => $patient->id,
            'social_name'  => $patient->social_name ?: $patient->name,
            'chat_id'      => $patient->chat_id,
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

    /**
     * Calcula o IMC automaticamente
     * IMC = peso(kg) / (altura(m)^2)
     */
    public function calculateBMI(): ?float
    {
        if (!$this->weight || !$this->height) {
            return null;
        }

        $heightInMeters = $this->height / 100;
        return $this->weight / ($heightInMeters ** 2);
    }

}

