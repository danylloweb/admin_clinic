<?php

namespace App\Presenters;

use App\Transformers\PatientTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PatientPresenter.
 *
 * @package namespace App\Presenters;
 */
class PatientPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PatientTransformer();
    }
}
