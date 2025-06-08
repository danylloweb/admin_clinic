<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FollowUpScheduleMessage.
 *
 * @package namespace App\Entities;
 */
class FollowUpScheduleMessage extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'sales_order_id',
        'follow_up_schedule_id',
        'follow_up_message_id',
        'date',
        'time',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
    /**
     * @return BelongsTo
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * @return BelongsTo
     */
    public function followUpMessage(): BelongsTo
    {
        return $this->belongsTo(FollowUpMessage::class);
    }
}
