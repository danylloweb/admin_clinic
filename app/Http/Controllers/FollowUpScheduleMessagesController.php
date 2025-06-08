<?php

namespace App\Http\Controllers;

use App\Services\FollowUpScheduleMessageService;
use App\Validators\FollowUpScheduleMessageValidator;

/**
 * Class FollowUpScheduleMessagesController.
 *
 * @package namespace App\Http\Controllers;
 */
class FollowUpScheduleMessagesController extends Controller
{
    /**
     * @var FollowUpScheduleMessageService
     */
    protected $service;

    /**
     * @var FollowUpScheduleMessageValidator
     */
    protected $validator;

    /**
     * FollowUpScheduleMessagesController constructor.
     *
     * @param FollowUpScheduleMessageService $service
     * @param FollowUpScheduleMessageValidator $validator
     */
    public function __construct(FollowUpScheduleMessageService $service, FollowUpScheduleMessageValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

}
