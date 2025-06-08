<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByBirthDateCriteria
 * @package namespace App\Criteria;
 */
class FilterByBirthDateCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $birth_date = $this->request->query->get('birth_date');
        if ($birth_date){
            $model = $model->whereRaw("MONTH(birth_date) = ?", [date('m')]);
        }
        return $model;
    }

}
