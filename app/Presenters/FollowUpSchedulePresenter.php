<?php

namespace App\Presenters;

use App\Transformers\FollowUpScheduleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FollowUpSchedulePresenter.
 *
 * @package namespace App\Presenters;
 */
class FollowUpSchedulePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new FollowUpScheduleTransformer();
    }
}
