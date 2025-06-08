<?php

namespace App\Http\Controllers;

use App\Services\SalesOrderItemService;
use App\Validators\SalesOrderItemValidator;

/**
 * Class SalesOrderItemsController.
 *
 * @package namespace App\Http\Controllers;
 */
class SalesOrderItemsController extends Controller
{
    /**
     * @var SalesOrderItemService
     */
    protected $service;

    /**
     * @var SalesOrderItemValidator
     */
    protected $validator;

    /**
     * SalesOrderItemsController constructor.
     *
     * @param SalesOrderItemService $service
     * @param SalesOrderItemValidator $validator
     */
    public function __construct(SalesOrderItemService $service, SalesOrderItemValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

}
