<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SalesOrderItem.
 *
 * @package namespace App\Entities;
 */
class SalesOrderItem extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_id',
        'procedure_id',
        'procedure_name',
        'price',
        'qty',
        'schedule_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @return mixed
     */
    public function getPatientId(): mixed
    {
        $sale = SalesOrder::find($this->attributes['sales_order_id']);
        return $sale->patient_id;
    }

    /**
     * @return BelongsTo
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * @return BelongsTo
     */
    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }
}
