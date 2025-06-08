<?php

namespace App\Presenters;

use App\Transformers\FollowUpScheduleMessageTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FollowUpScheduleMessagePresenter.
 *
 * @package namespace App\Presenters;
 */
class FollowUpScheduleMessagePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new FollowUpScheduleMessageTransformer();
    }
}
