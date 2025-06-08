<?php

namespace App\Presenters;

use App\Transformers\CampaignTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class CampaignPresenter.
 *
 * @package namespace App\Presenters;
 */
class CampaignPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new CampaignTransformer();
    }
}
