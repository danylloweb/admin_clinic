<?php

namespace App\Repositories;

use App\Presenters\PatientPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PatientRepository;
use App\Entities\Patient;
use App\Validators\PatientValidator;

/**
 * Class PatientRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PatientRepositoryEloquent extends AppRepository implements PatientRepository
{
    protected $fieldSearchable = [
        'name'          => 'like',
        'phone'          => 'like',
    ];

    /**
     * Regras para busca
     *
     * @var array
     */
    protected $fieldsRules = [
        'name'           => ['string', 'max:20'],
        'phone'           => ['numeric', 'max:12'],
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Patient::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return PatientValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return PatientPresenter::class;
    }

}
