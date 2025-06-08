<?php

namespace App\Presenters;

use App\Transformers\LeadTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LeadPresenter.
 *
 * @package namespace App\Presenters;
 */
class LeadPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new LeadTransformer();
    }
}
