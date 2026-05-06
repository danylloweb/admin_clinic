<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PatientMedicalRecord.
 *
 * @package namespace App\Entities;
 */
class PatientMedicalRecord extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'access_token',
        'token_generated_at',
        'submitted_at',
        'emergency_contact_name',
        'emergency_contact_phone',
        'treatment_goals',
        'type_of_food',
        'consume_alcohol',
        'smoke',
        'practice_physical_activity',
        'liters_of_water_per_day',
        'use_medication',
        'have_allergies',
        'use_anabolic_hormones',
        'children',
        'pacemaker',
        'metal_prosthesis',
        'diabetes',
        'oncology',
        'arterial_hypertension',
        'blood_type',
        'observation',
        'lgpd_consent',
        'signature_name',
    ];

    protected $casts = [
        'token_generated_at' => 'datetime',
        'submitted_at' => 'datetime',
        'lgpd_consent' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function hasPendingToken(): bool
    {
        return !empty($this->access_token) && $this->submitted_at === null;
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function publicUrl(): ?string
    {
        if (!$this->hasPendingToken()) {
            return null;
        }

        return route('patient-medical-record.show', ['token' => $this->access_token]);
    }

}
