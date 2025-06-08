<?php

namespace App\Criterias;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByUserLoggedCriteria
 * @package namespace App\Criteria;
 */
class FilterByUserLoggedCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $userId = $this->request->query->get('user_id');
        if (is_numeric($userId)) {
            $model = $model->where('punter_id', $userId);
        }
        return $model;
    }
}
