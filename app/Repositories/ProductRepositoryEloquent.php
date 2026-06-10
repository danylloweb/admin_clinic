<?php

namespace App\Repositories;

use App\Presenters\ProductPresenter;
use App\Validators\ProductValidator;
use App\Entities\Product;

class ProductRepositoryEloquent extends AppRepository implements ProductRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
        'internal_code' => 'like',
        'ean_code' => 'like',
    ];

    protected $fieldsRules = [
        'name' => ['string', 'max:255'],
    ];

    public function model()
    {
        return Product::class;
    }

    public function validator()
    {
        return ProductValidator::class;
    }

    public function presenter()
    {
        return ProductPresenter::class;
    }
}

