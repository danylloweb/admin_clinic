<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\UserTypeRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * UserTypeService
 */
class UserTypeService extends AppService
{
    /**
     * @var UserTypeRepository
     */
    protected $repository;

    /**
     * @param UserTypeRepository $repository
     */
    public function __construct(UserTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws RepositoryException
     */
    public function all(int $limit = 20)
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

}
