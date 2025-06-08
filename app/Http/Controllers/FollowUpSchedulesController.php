<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\FollowUpScheduleCreateRequest;
use App\Http\Requests\FollowUpScheduleUpdateRequest;
use App\Repositories\FollowUpScheduleRepository;
use App\Validators\FollowUpScheduleValidator;

/**
 * Class FollowUpSchedulesController.
 *
 * @package namespace App\Http\Controllers;
 */
class FollowUpSchedulesController extends Controller
{
    /**
     * @var FollowUpScheduleRepository
     */
    protected $repository;

    /**
     * @var FollowUpScheduleValidator
     */
    protected $validator;

    /**
     * FollowUpSchedulesController constructor.
     *
     * @param FollowUpScheduleRepository $repository
     * @param FollowUpScheduleValidator $validator
     */
    public function __construct(FollowUpScheduleRepository $repository, FollowUpScheduleValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


}
