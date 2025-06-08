<?php

namespace App\Presenters;

use App\Transformers\FollowUpMessageTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FollowUpMessagePresenter.
 *
 * @package namespace App\Presenters;
 */
class FollowUpMessagePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new FollowUpMessageTransformer();
    }
}
