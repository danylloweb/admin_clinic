<?php

namespace App\Repositories;

use App\Presenters\SchedulePresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ScheduleRepository;
use App\Entities\Schedule;
use App\Validators\ScheduleValidator;

/**
 * Class ScheduleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ScheduleRepositoryEloquent extends AppRepository implements ScheduleRepository
{
    protected $fieldSearchable = [
        'id'             => '=',
        'patient.name'   => 'like',
        'patient.phone'  => 'like',
        'procedure.name' => 'like'
    ];



    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Schedule::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ScheduleValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return SchedulePresenter::class;
    }

    /**
     * @return array
     */
    public function getPatientsByLaser(): array
    {
        $list_patients = [];
        $query = $this->model();
        $patientsWithSchedules = $query::where('date', '2023-12-09')
            ->whereHas('procedure', function ($query) {
                $query->where('procedure_type_id', 4);
            })
            ->with('patient')
            ->get()
            ->groupBy('patient.id');

        foreach ($patientsWithSchedules as $schedules) {
            $list_patients[] = $schedules[0]->patient->phone;
        }
        return $list_patients;
    }
}
