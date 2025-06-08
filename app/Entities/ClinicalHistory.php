<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ClinicalHistory.
 *
 * @package namespace App\Entities;
 */
class ClinicalHistory extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
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
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

}
