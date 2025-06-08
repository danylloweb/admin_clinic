<?php

namespace App\Presenters;

use App\Transformers\PaymentOrderTransformer;
use League\Fractal\TransformerAbstract;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PaymentOrderPresenter.
 *
 * @package namespace App\Presenters;
 */
class PaymentOrderPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return TransformerAbstract
     */
    public function getTransformer()
    {
        return new PaymentOrderTransformer();
    }
}
