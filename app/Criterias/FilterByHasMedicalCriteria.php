<?php

namespace App\Criterias;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByHasMedicalCriteria
 * @package namespace App\Criteria;
 */
class FilterByHasMedicalCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $has_medical = $this->request->query->get('has_medical');
        if ($has_medical) {
            $model = $model->where('has_medical', $has_medical);
        }
        return $model;
    }
}
