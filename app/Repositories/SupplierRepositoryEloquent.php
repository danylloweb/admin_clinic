<?php

namespace App\Repositories;

use App\Entities\Supplier;

class SupplierRepositoryEloquent extends AppRepository implements SupplierRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
        'cnpj' => 'like',
    ];

    public function model()
    {
        return Supplier::class;
    }
}

