<?php

namespace App\Presenters;

use App\Transformers\PasswordResetTransformer;
use League\Fractal\TransformerAbstract;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PasswordResetPresenter.
 *
 * @package namespace App\Presenters;
 */
class PasswordResetPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return TransformerAbstract
     */
    public function getTransformer()
    {
        return new PasswordResetTransformer();
    }
}
