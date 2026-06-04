<?php

namespace App\Services;

use App\Entities\Gift;

/**
 * DashboardService
 */
class GiftService
{

    public function __construct(private readonly Gift $repository){}

    /**
     * @param array $data
     * @return Gift
     */
    public function create(array $data): Gift
    {
        return $this->repository->create($data);
    }

    public function all(int $limit = 20)
    {
        return $this->repository->paginate($limit);
    }
}
