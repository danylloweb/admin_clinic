<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByDateScheduleCriteria
 * @package namespace App\Criteria;
 */
class FilterByDateScheduleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $date_schedule = $this->request->query->get('date_schedule');
        $month         = $this->request->query->get('month');
        $patient_id    = $this->request->query->get('patient_id');
        $procedure_id  = $this->request->query->get('procedure_id');
        if ($month) {
            $month_date   = Carbon::create($month);
            $startOfMonth = $month_date->format('Y-m-d');
            $endOfMonth   = $month_date->copy()->endOfMonth()->format('Y-m-d');
            $model        = $model->where('date', '>=', $startOfMonth)->where('date', '<=', $endOfMonth);
        } elseif ($date_schedule) {
            $model = $model->where('date', Carbon::create($date_schedule)->format('Y-m-d'));
        }elseif ($patient_id || $procedure_id){
            return $model;
        }else{
            $model = $model->whereIn('date', $this->getDatesOfWeek());
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
