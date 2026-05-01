<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class SalesOrderValidator.
 *
 * @package namespace App\Validators;
 */
class SalesOrderValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
//        ValidatorInterface::RULE_CREATE => [
//            'patient_id' => 'required|integer|min:1',
//            'user_id' => 'required|integer|min:1',
//            'type_payment' => 'required|integer|in:1,2,3,4',
//            'brand_card' => 'required|integer|min:1|max:3',
//            'qty_installments' => 'required|integer|min:1|max:10',
//            'items' => 'required|array|min:1',
//            'items.*.procedure_id' => 'required|integer|min:1',
//            'items.*.qty' => 'required|integer|min:1',
//        ],
//        ValidatorInterface::RULE_UPDATE => [
//            'qty_installments' => 'sometimes|integer|min:1|max:10',
//            'type_payment' => 'sometimes|integer|in:1,2,3,4',
//            'brand_card' => 'sometimes|integer|min:1|max:3',
//            'partial_amount' => 'sometimes|numeric|min:0',
//            'status' => 'sometimes|integer',
//        ],
    ];
}
