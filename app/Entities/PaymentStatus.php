<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PaymentStatus.
 *
 * @package namespace App\Entities;
 */
class PaymentStatus extends Model implements Transformable
{
    use TransformableTrait;

    const PENDING = 1;
    const APPROVED = 2;
    const CANCELLED = 3;
    const REJECTED = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public static function status($status): int
    {
        switch ($status) {
            case 'pensing':
                $result = self::PENDING;
                break;
            case 'approved':
                $result = self::APPROVED;
                break;
            case 'cancelled':
                $result = self::CANCELLED;
                break;
            default :
                $result = self::REJECTED;
        }

        return $result;
    }

    public static function statusName($status): string
    {
        switch ($status) {
            case self::PENDING:
                $result = "Pendente";
                break;
            case self::APPROVED:
                $result = "Aprovado";
                break;
            case self::CANCELLED:
                $result = "Cancelado";
                break;
            default :
                $result = "Rejeitado";
        }

        return $result;
    }
}
