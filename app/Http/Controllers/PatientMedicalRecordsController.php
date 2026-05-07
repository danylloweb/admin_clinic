<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\PatientMedicalRecordCreateRequest;
use App\Http\Requests\PatientMedicalRecordUpdateRequest;
use App\Repositories\PatientMedicalRecordRepository;
use App\Validators\PatientMedicalRecordValidator;

/**
 * Class PatientMedicalRecordsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PatientMedicalRecordsController extends Controller
{
    /**
     * @var PatientMedicalRecordRepository
     */
    protected $repository;

    /**
     * @var PatientMedicalRecordValidator
     */
    protected $validator;

    /**
     * PatientMedicalRecordsController constructor.
     *
     * @param PatientMedicalRecordRepository $repository
     * @param PatientMedicalRecordValidator $validator
     */
    public function __construct(PatientMedicalRecordRepository $repository, PatientMedicalRecordValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


}
