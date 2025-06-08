<?php

namespace App\Presenters;

use App\Transformers\ClinicalHistoryTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ClinicalHistoryPresenter.
 *
 * @package namespace App\Presenters;
 */
class ClinicalHistoryPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ClinicalHistoryTransformer();
    }
}
