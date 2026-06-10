<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

class ProductValidator extends LaravelValidator
{
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'internal_code' => 'required|string|max:255|unique:products',
            'ean_code' => 'nullable|string|unique:products',
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|url|max:2048',
            'category_type' => 'required|in:cosmetic,dermocosmetic,medicine,botulinum_toxin,filler,biostimulator,enzyme,equipment,disposable_material,consumable_material,input,other',
            'unit_measure' => 'required|in:unit,box,ampule,flask,syringe,ml,mg,g,kg',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'nullable|boolean',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'internal_code' => 'required|string|max:255',
            'ean_code' => 'nullable|string',
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|url|max:2048',
            'category_type' => 'required|in:cosmetic,dermocosmetic,medicine,botulinum_toxin,filler,biostimulator,enzyme,equipment,disposable_material,consumable_material,input,other',
            'unit_measure' => 'required|in:unit,box,ampule,flask,syringe,ml,mg,g,kg',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'nullable|boolean',
        ],
    ];
}

