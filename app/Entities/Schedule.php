<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Schedule.
 *
 * @package namespace App\Entities;
 */
class Schedule extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'procedure_id',
        'patient_id',
        'date',
        'time',
        'status',
        'observation_status',
        'professional_id',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * @return BelongsTo
     */
    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'professional_id');
    }

    /**
     * @return array
     */
    public function getSaleOrderStatus(): array
    {
        $item = SalesOrderItem::query()->where("schedule_id",$this->attributes['id'])->first();
        if ($item){
            $sale = SalesOrder::find($item->sales_order_id);
            return ['status' => $sale->status,'id' => $sale->id];
        }
        return ['status' => 1,'id' => 0];
    }
}
