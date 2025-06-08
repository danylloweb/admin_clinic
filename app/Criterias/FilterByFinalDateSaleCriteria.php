<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByFinalDateSaleCriteria
 * @package namespace App\Criteria;
 */
class FilterByFinalDateSaleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $final_date = $this->request->query->get('final_date');

        if ($final_date) {
            $final = Carbon::create($final_date);
            $model = $model->where('created_at','<=', $final->endOfDay());
        }

        return $model;
    }

    /**
     * @return array
     */
    private function getDatesOfWeek(): array
    {
        $dates     = [];
        $startWeek = Carbon::now()->startOfWeek();
        for ($i = 0; $i < 5; $i++) {
            $day = $startWeek->copy()->addWeekday($i);
            if ($day->isWeekday()) {
                $dates[] = $day->toDateString();
            }
        }
        unset($dates[5]);
        return $dates;
    }
}
