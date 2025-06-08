<?php

namespace App\Criterias;


use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByPatientScheduleCriteria
 * @package namespace App\Criteria;
 */
class FilterByPatientScheduleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $patient_id = $this->request->query->get('patient_id');
        if ($patient_id) {
            $model = $model->where('patient_id', $patient_id);
        }
        return $model;
    }
}
