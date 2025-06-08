<?php

namespace App\Http\Controllers;

use App\Services\ProcedureTypeService;
use App\Validators\ProcedureTypeValidator;

/**
 * Class ProcedureTypesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProcedureTypesController extends Controller
{
    /**
     * @var ProcedureTypeService
     */
    protected $service;

    /**
     * @var ProcedureTypeValidator
     */
    protected $validator;

    /**
     * ProcedureTypesController constructor.
     *
     * @param ProcedureTypeService $service
     * @param ProcedureTypeValidator $validator
     */
    public function __construct(ProcedureTypeService $service, ProcedureTypeValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }


}
