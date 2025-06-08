<?php

namespace App\Criterias;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByProcedureStatusActiveCriteria
 * @package namespace App\Criteria;
 */
class FilterByProcedureStatusActiveCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $status = $this->request->query->get('status');
        if (is_numeric($status)) {
            $model = $model->where('status', 1);
        }
        return $model;
    }
}
