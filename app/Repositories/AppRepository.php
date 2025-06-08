<?php

namespace App\Repositories;

use App\Criterias\AppRequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BankAccountRepositoryEloquent
 * @package namespace App\Repositories;
 */
abstract class AppRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldsRules = [];

    /**
     * Get Fields Types
     *
     * @return array
     */
    public function getFieldsRules()
    {
        return $this->fieldsRules;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(AppRequestCriteria::class));
    }
}
