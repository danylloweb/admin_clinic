<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByDateScheduleCriteria
 * @package namespace App\Criteria;
 */
class FilterByDateStartScheduleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $start = $this->request->query->get('start') ?: $this->request->query->get('start_date');

        if (!$start) {
            return $model;
        }

        $start = Carbon::create($start)->startOfDay()->format('Y-m-d');
        $model = $model->where('date', '>=', $start);
        return $model;
    }

}
