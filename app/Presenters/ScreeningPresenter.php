<?php

namespace App\Presenters;

use App\Transformers\ScreeningTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ScreeningPresenter.
 *
 * @package namespace App\Presenters;
 */
class ScreeningPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ScreeningTransformer();
    }
}
