<?php

namespace App\Http\Controllers;

use App\Services\ClinicalHistoryService;
use App\Validators\ClinicalHistoryValidator;

/**
 * Class ClinicalHistoriesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ClinicalHistoriesController extends Controller
{
    /**
     * @var ClinicalHistoryService
     */
    protected $service;

    /**
     * @var ClinicalHistoryValidator
     */
    protected $validator;

    /**
     * ClinicalHistoriesController constructor.
     *
     * @param ClinicalHistoryService $service
     * @param ClinicalHistoryValidator $validator
     */
    public function __construct(ClinicalHistoryService $service, ClinicalHistoryValidator $validator)
    {
        $this->service = $service;
        $this->validator  = $validator;
    }

}
