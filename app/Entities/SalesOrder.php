<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SalesOrder.
 *
 * @package namespace App\Entities;
 */
class SalesOrder extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'amount',
        'partial_amount',
        'discount',
        'author_id',
        'qty',
        'qty_installments',
        'type_payment',
        'status',
        'brand_card',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    const IS_PACKAGE = 1;
    /**
     * @return BelongsTo
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrderItems():\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    /**
     * @return array[]
     */
    public function getTaxByMasterCard(): array
    {
        return [
            ["installment" => 1, "tax" => 0.0349],
            ["installment" => 2, "tax" => 0.0899],
            ["installment" => 3, "tax" => 0.1099],
            ["installment" => 4, "tax" => 0.1199],
            ["installment" => 5, "tax" => 0.1299],
            ["installment" => 6, "tax" => 0.1399],
            ["installment" => 7, "tax" => 0.1499],
            ["installment" => 8, "tax" => 0.1599],
            ["installment" => 9, "tax" => 0.1699],
            ["installment" => 10,"tax" => 0.1799]
        ];
    }

    /**
     * @return array[]
     */
    public function getTaxByEloCard(): array
    {
        return [
            ["installment" => 1, "tax" => 0.0468],
            ["installment" => 2, "tax" => 0.1038],
            ["installment" => 3, "tax" => 0.1238],
            ["installment" => 4, "tax" => 0.1338],
            ["installment" => 5, "tax" => 0.1438],
            ["installment" => 6, "tax" => 0.1538],
            ["installment" => 7, "tax" => 0.1638],
            ["installment" => 8, "tax" => 0.1738],
            ["installment" => 9, "tax" => 0.1838],
            ["installment" => 10,"tax" => 0.1938]
        ];
    }

    /**
     * @return int|mixed
     */
    public function getInstallmentTax(): mixed
    {
        $brand       =  $this->attributes['brand_card'];
        $installment = $this->attributes['qty_installments'];
        $taxRates    = ($brand < 2) ? $this->getTaxByMasterCard() : $this->getTaxByEloCard();

        foreach ($taxRates as $taxData) {
            if ($taxData['installment'] === (int)$installment) {
                return $taxData['tax'];
            }
        }
        return 0;
    }

    /**
     * @return float
     */
    public function getDebitAmount(): float
    {
        $brand  = $this->attributes['brand_card'];
        $amount = $this->attributes['amount'];
        $tax    = ($brand <= 2) ? 0.0169 : 0.0288;
        return $amount + ($amount * $tax);
    }
}
