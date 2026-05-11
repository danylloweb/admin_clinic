<?php

namespace App\Presenters;

use App\Transformers\BodyEvaluationTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BodyEvaluationPresenter.
 *
 * @package namespace App\Presenters;
 */
class BodyEvaluationPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BodyEvaluationTransformer();
    }
}
