<?php

namespace App\Criterias;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByPatientIdCriteria
 * @package namespace App\Criteria;
 */
class FilterByPatientIdCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $patient_id = $this->request->query->get('patient_id');
        if (is_numeric($patient_id)) {
            $model = $model->where('patient_id', $patient_id);
        }
        return $model;
    }
}
