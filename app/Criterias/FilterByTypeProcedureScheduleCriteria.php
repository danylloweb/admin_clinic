<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByScheduleStatusCriteria
 * @package namespace App\Criteria;
 */
class FilterByTypeProcedureScheduleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $procedure_type_id = $this->request->query->get('procedure_type_id');
        if ($procedure_type_id) {
            $model = $model->whereHas('procedure', function($query) use($procedure_type_id){
                $query->where('procedure_type_id', $procedure_type_id);
            });
        }
        return $model;
    }
}
