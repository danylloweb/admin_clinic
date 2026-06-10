<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

class ProductLotValidator extends LaravelValidator
{
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'product_id' => 'required|exists:products,id',
            'batch_number' => 'required|string|max:255',
            'quantity_received' => 'required|integer|min:1',
            'expiration_date' => 'required|date',
            'received_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'batch_number' => 'sometimes|string|max:255',
            'quantity_received' => 'sometimes|integer|min:1',
            'expiration_date' => 'sometimes|date',
            'received_date' => 'sometimes|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ],
    ];
}

