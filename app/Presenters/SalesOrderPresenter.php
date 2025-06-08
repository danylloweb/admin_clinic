<?php

namespace App\Presenters;

use App\Transformers\SalesOrderTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class SalesOrderPresenter.
 *
 * @package namespace App\Presenters;
 */
class SalesOrderPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new SalesOrderTransformer();
    }
}
