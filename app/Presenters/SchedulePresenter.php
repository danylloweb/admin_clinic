<?php

namespace App\Presenters;

use App\Transformers\ScheduleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class SchedulePresenter.
 *
 * @package namespace App\Presenters;
 */
class SchedulePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ScheduleTransformer();
    }
}
