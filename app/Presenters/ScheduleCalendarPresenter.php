<?php

namespace App\Presenters;

use App\Transformers\ScheduleCalendarTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ScheduleCalendarPresenter.
 *
 * @package namespace App\Presenters;
 */
class ScheduleCalendarPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ScheduleCalendarTransformer();
    }
}
