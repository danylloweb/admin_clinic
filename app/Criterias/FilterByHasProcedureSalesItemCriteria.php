<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByHasProcedureSalesItemCriteria
 * @package namespace App\Criteria;
 */
class FilterByHasProcedureSalesItemCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $procedure_id = $this->request->query->get('procedure_id');
        if ($procedure_id) {
            $model = $model->whereHas('salesOrderItems', function($query) use($procedure_id){
                $query->where('procedure_id', $procedure_id);
            });
        }
        return $model;
    }
}
