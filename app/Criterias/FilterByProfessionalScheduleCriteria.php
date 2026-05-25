<?php

namespace App\Criterias;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByProfessionalScheduleCriteria
 * @package namespace App\Criteria;
 */
class FilterByProfessionalScheduleCriteria extends AppCriteria implements CriteriaInterface
{
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $professional_id = $this->request->query->get('professional_id');
        if ($professional_id) {
            $model = $model->where('professional_id', $professional_id);
        }
        return $model;
    }
}

