<?php

namespace App\Criterias;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;

/**
 * Class AppCriteria
 * @package App\Criterias
 */
abstract class AppCriteria implements CriteriaInterface
{
    /** @var Request */
    protected Request $request;

    /**
     * AppCriteria constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
