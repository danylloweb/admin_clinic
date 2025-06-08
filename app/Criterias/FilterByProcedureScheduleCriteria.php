<?php

namespace App\Criterias;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByProcedureScheduleCriteria
 * @package namespace App\Criteria;
 */
class FilterByProcedureScheduleCriteria extends AppCriteria implements CriteriaInterface
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
            $model = $model->where('procedure_id', $procedure_id);
        }
        return $model;
    }
}
