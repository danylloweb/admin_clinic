<?php

namespace App\Repositories;

use App\Entities\ProcedureConsumption;

class ProcedureConsumptionRepositoryEloquent extends AppRepository implements ProcedureConsumptionRepository
{
    protected $fieldSearchable = [
        'product_id',
        'patient_id',
    ];

    public function model()
    {
        return ProcedureConsumption::class;
    }
}

