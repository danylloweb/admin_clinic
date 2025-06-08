<?php

namespace App\Presenters;

use App\Transformers\SalesOrderDetailTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class SalesOrderDetailPresenter.
 *
 * @package namespace App\Presenters;
 */
class SalesOrderDetailPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new SalesOrderDetailTransformer();
    }
}
