<?php

namespace App\Presenters;

use App\Transformers\AdvertTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class AdvertPresenter.
 *
 * @package namespace App\Presenters;
 */
class AdvertPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new AdvertTransformer();
    }
}
