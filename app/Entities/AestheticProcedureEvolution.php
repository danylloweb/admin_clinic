<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AestheticProcedureEvolution.
 *
 * @package namespace App\Entities;
 */
class AestheticProcedureEvolution extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $table = 'aesthetic_procedure_evolutions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'schedule_id',
        'patient_id',
        'professional_id',
        'procedure_name',
        'start_date',
        'evolution_sessions',
        'photo_before',
        'photo_after',
        'result_evaluation',
        'patient_signature',
        'professional_signature',
        'signed_at',
    ];

    protected $casts = [
        'schedule_id' => 'integer',
        'patient_id' => 'integer',
        'professional_id' => 'integer',
        'start_date' => 'date',
        'evolution_sessions' => 'array',
        'signed_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id', 'id');
    }

}
