<?php

namespace App\Presenters;

use App\Transformers\AestheticProcedureEvolutionTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class AestheticProcedureEvolutionPresenter.
 *
 * @package namespace App\Presenters;
 */
class AestheticProcedureEvolutionPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new AestheticProcedureEvolutionTransformer();
    }
}
