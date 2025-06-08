<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UsersController extends Controller
{
    /**
     * @var UserService
     */
    protected $service;

    /**
     * UsersController constructor.
     *
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @param UserCreateRequest $request
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function newUser(UserCreateRequest $request): JsonResponse
    {
        return parent::store($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserLogged(Request $request): JsonResponse
    {
        try {
            $user_id = $request->get('user_id');
            $user = Cache::store('redis')->tags('user_id')->remember($user_id, 48, function () use ($user_id) {
                return $this->show($user_id);
            });

            return response()->json($user);
        } catch (Exception $exception) {
            return $this->sendBadResposnse($exception);
        }
    }

    /**
     * @param int $phone
     * @return JsonResponse
     */
    public function checkPhone(int $phone): JsonResponse
    {
        try {
            return response()->json($this->service->checkPhone($phone));
        } catch (Exception $exception) {
            return $this->sendBadResposnse($exception);
        }
    }
}
