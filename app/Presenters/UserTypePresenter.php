<?php

namespace App\Presenters;

use App\Transformers\UserTypeTransformer;
use League\Fractal\TransformerAbstract;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserTypePresenter.
 *
 * @package namespace App\Presenters;
 */
class UserTypePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserTypeTransformer();
    }
}
