<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByTypePackageScheduleCriteria
 * @package namespace App\Criteria;
 */
class FilterByTypePackageScheduleCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $is_package = $this->request->query->get('is_package');
        if (is_numeric($is_package)) {
            $model = $model->where('is_package', $is_package);
        }
        return $model;
    }
}
