<?php

namespace App\Presenters;

use App\Transformers\SalesOrderItemTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class SalesOrderItemPresenter.
 *
 * @package namespace App\Presenters;
 */
class SalesOrderItemPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new SalesOrderItemTransformer();
    }
}
