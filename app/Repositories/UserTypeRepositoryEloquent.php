<?php

namespace App\Repositories;

use App\Entities\UserType;
use App\Presenters\UserTypePresenter;

/**
 * Class UserTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserTypeRepositoryEloquent extends AppRepository implements UserTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return UserType::class;
    }

    /**
     * @return string
     */
    public function presenter(): string
    {
        return UserTypePresenter::class;
    }
}
