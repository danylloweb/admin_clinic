<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByStartDateSaleCriteria
 * @package namespace App\Criteria;
 */
class FilterByStartDateSaleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $start_date = $this->request->query->get('start_date');

        if ($start_date) {
            $start = Carbon::create($start_date);
            $model = $model->where('created_at', '>=', $start->startOfDay());
        }

        return $model;
    }


}
