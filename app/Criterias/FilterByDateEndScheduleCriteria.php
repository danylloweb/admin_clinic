<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByDateScheduleCriteria
 * @package namespace App\Criteria;
 */
class FilterByDateEndScheduleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $end   = $this->request->query->get('end');
        $end   = Carbon::create($end)->addDay()->format('Y-m-d');
        $model = $model->where('date', '<', $end);
        return $model;
    }

}
