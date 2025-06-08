<?php

namespace App\Http\Controllers;

use App\Repositories\PasswordResetRepository;

/**
 * Class PasswordResetsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PasswordResetsController extends Controller
{
    /**
     * @var PasswordResetRepository
     */
    protected $repository;

    /**
     * PasswordResetsController constructor.
     *
     * @param PasswordResetRepository $repository
     */
    public function __construct(PasswordResetRepository $repository)
    {
        $this->repository = $repository;
    }
}
