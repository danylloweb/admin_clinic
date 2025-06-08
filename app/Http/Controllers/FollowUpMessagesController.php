<?php

namespace App\Http\Controllers;

use App\Services\FollowUpMessageService;
use App\Validators\FollowUpMessageValidator;
use Illuminate\Http\Request;

/**
 * Class FollowUpMessagesController.
 *
 * @package namespace App\Http\Controllers;
 */
class FollowUpMessagesController extends Controller
{
    /**
     * @var FollowUpMessageService
     */
    protected $service;

    /**
     * @var FollowUpMessageValidator
     */
    protected $validator;

    /**
     * FollowUpMessagesController constructor.
     *
     * @param FollowUpMessageService $service
     * @param FollowUpMessageValidator $validator
     */
    public function __construct(FollowUpMessageService $service, FollowUpMessageValidator $validator)
    {
        $this->service = $service;
        $this->validator  = $validator;
    }

    public function sendMessageDirect(Request $request)
    {
        return response()->json($this->service->sendMessageDirect($request->all()));
    }
}
