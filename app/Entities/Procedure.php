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

    /**
     * @return array[]
     */
    public function getTaxByMasterCard(): array
    {
        return [
            ["installment" => 1, "tax" => 0.0449],
            ["installment" => 2, "tax" => 0.0999],
            ["installment" => 3, "tax" => 0.1199],
            ["installment" => 4, "tax" => 0.1299],
            ["installment" => 5, "tax" => 0.1399],
            ["installment" => 6, "tax" => 0.1499],
            ["installment" => 7, "tax" => 0.1599],
            ["installment" => 8, "tax" => 0.1699],
            ["installment" => 9, "tax" => 0.1799],
            ["installment" => 10,"tax" => 0.1899]
        ];
    }

    /**
     * @return array[]
     */
    public function getTaxByEloCard(): array
    {
        return [
            ["installment" => 1, "tax" => 0.0568],
            ["installment" => 2, "tax" => 0.1138],
            ["installment" => 3, "tax" => 0.1338],
            ["installment" => 4, "tax" => 0.1438],
            ["installment" => 5, "tax" => 0.1538],
            ["installment" => 6, "tax" => 0.1638],
            ["installment" => 7, "tax" => 0.1738],
            ["installment" => 8, "tax" => 0.1838],
            ["installment" => 9, "tax" => 0.1938],
            ["installment" => 10,"tax" => 0.2038]
        ];
    }

    /**
     * @return int|mixed
     */
    public function getInstallmentTax(): mixed
    {
        $installment = 1;
        $taxRates    = $this->getTaxByMasterCard();

        foreach ($taxRates as $taxData) {
            if ($taxData['installment'] === (int)$installment) {
                return $taxData['tax'];
            }
        }
        return 0;
    }



}
