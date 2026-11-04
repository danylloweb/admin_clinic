<?php

namespace App\Criterias;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByPatientIdCriteria
 * @package namespace App\Criterias;
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
        $patientId = $this->request->query->get('patient_id');
        if (is_numeric($patientId)) {
            $model = $model->where('patient_id', $patientId);
        }
        return $model;
    }
}
