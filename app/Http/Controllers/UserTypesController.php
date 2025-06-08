<?php

namespace App\Http\Controllers;

use App\Services\UserTypeService;

/**
 * Class UserTypesController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserTypesController extends Controller
{
    /**
     * @var UserTypeService
     */
    protected $service;

    /**
     * UserTypesController constructor.
     *
     * @param UserTypeService $service
     */
    public function __construct(UserTypeService $service)
    {
        $this->service = $service;
    }

}
