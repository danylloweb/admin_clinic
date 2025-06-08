<?php

namespace App\Http\Controllers;

use App\Services\LeadService;
use App\Validators\LeadValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class LeadsController.
 *
 * @package namespace App\Http\Controllers;
 */
class LeadsController extends Controller
{
    /**
     * @var LeadService
     */
    protected $service;

    /**
     * @var LeadValidator
     */
    protected $validator;

    /**
     * LeadsController constructor.
     *
     * @param LeadService $service
     * @param LeadValidator $validator
     */
    public function __construct(LeadService $service, LeadValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    public function sendMessageAdvert(int $id)
    {
        return response()->json($this->service->sendMessageAdvert($id));
    }


}
