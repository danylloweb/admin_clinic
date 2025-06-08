<?php

namespace App\Repositories;

use App\Presenters\ClinicalHistoryPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ClinicalHistoryRepository;
use App\Entities\ClinicalHistory;
use App\Validators\ClinicalHistoryValidator;

/**
 * Class ClinicalHistoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ClinicalHistoryRepositoryEloquent extends AppRepository implements ClinicalHistoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ClinicalHistory::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ClinicalHistoryValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return ClinicalHistoryPresenter::class;
    }

}
