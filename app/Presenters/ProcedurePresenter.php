<?php

namespace App\Presenters;

use App\Transformers\ProcedureTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ProcedurePresenter.
 *
 * @package namespace App\Presenters;
 */
class ProcedurePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ProcedureTransformer();
    }
}
