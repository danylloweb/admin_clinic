<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Validators\CampaignValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CampaignsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CampaignsController extends Controller
{
    /**
     * @var CampaignService
     */
    protected $service;

    /**
     * @var CampaignValidator
     */
    protected $validator;

    /**
     * CampaignsController constructor.
     *
     * @param CampaignService $service
     * @param CampaignValidator $validator
     */
    public function __construct(CampaignService $service, CampaignValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

}
