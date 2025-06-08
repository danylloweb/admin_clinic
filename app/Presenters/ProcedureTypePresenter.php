<?php

namespace App\Presenters;

use App\Transformers\ProcedureTypeTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ProcedureTypePresenter.
 *
 * @package namespace App\Presenters;
 */
class ProcedureTypePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ProcedureTypeTransformer();
    }
}
