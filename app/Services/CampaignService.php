<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\CampaignRepository;

/**
 * CampaignService
 */
class CampaignService extends AppService
{
    /**
     * @var CampaignRepository
     */
    protected $repository;

    /**
     * @param CampaignRepository $repository
     */
    public function __construct(CampaignRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function all(int $limit = 20): mixed
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

}
