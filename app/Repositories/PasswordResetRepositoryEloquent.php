<?php

namespace App\Repositories;

use App\Entities\PasswordReset;
use App\Presenters\PasswordResetPresenter;

/**
 * Class PasswordResetRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PasswordResetRepositoryEloquent extends AppRepository implements PasswordResetRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return PasswordReset::class;
    }

    /**
     * @return string
     */
   public function presenter(): string
   {
       return PasswordResetPresenter::class;
   }
}
