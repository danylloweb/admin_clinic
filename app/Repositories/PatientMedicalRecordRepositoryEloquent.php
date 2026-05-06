<?php

namespace App\Repositories;

use App\Presenters\PatientMedicalRecordPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PatientMedicalRecordRepository;
use App\Entities\PatientMedicalRecord;
use App\Validators\PatientMedicalRecordValidator;

/**
 * Class PatientMedicalRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PatientMedicalRecordRepositoryEloquent extends AppRepository implements PatientMedicalRecordRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PatientMedicalRecord::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return PatientMedicalRecordValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return PatientMedicalRecordPresenter::class;
    }

}
