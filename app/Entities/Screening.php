<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Screening.
 *
 * @package namespace App\Entities;
 */
class Screening extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'pregnant',
        'tanned_skin',
        'consume_alcohol',
        'sour_cream',
        'face_lotion',
        'arterial_tension',
        'weight',
        'glucose',
        'imc',
        'observation',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
