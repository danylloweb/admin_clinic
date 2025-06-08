<?php

namespace App\Criterias;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByTypePaymentCriteria
 * @package namespace App\Criteria;
 */
class FilterByTypePaymentCriteria extends AppCriteria implements CriteriaInterface
{

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $type_payment = $this->request->query->get('type_payment');
        if ($type_payment) {
            $model = $model->where('type_payment', $type_payment);
        }
        return $model;
    }
}
