<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Procedure.
 *
 * @package namespace App\Entities;
 */
class Procedure extends Model implements Transformable
{
    use TransformableTrait,SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'cost_price',
        'procedure_type_id',
        'execution_time',
        'minimum_amount_of_time',
        'non_competing',
        'price',
        'percentage_on_sale',
        'status',
        'observation',
        'step_by_step',
        'patient_instructions',
        'message_schedule',
        'author',
        'is_package',
        'qty',
        'unit_price',
        'message_schedule_after'
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function procedureType(): BelongsTo
    {
        return $this->belongsTo(ProcedureType::class);
    }

}
