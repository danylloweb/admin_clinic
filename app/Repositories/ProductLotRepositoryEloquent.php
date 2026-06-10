<?php

namespace App\Repositories;

use App\Validators\ProductLotValidator;
use App\Entities\ProductLot;

class ProductLotRepositoryEloquent extends AppRepository implements ProductLotRepository
{
    protected $fieldSearchable = [
        'batch_number' => 'like',
    ];

    public function model()
    {
        return ProductLot::class;
    }

    public function validator()
    {
        return ProductLotValidator::class;
    }
}

