<?php

namespace App\Presenters;

use App\Transformers\FacialEvaluationTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FacialEvaluationPresenter.
 *
 * @package namespace App\Presenters;
 */
class FacialEvaluationPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new FacialEvaluationTransformer();
    }
}
