<?php

namespace App\Presenters;

use App\Transformers\ProductTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

class ProductPresenter extends FractalPresenter
{
    public function getTransformer()
    {
        return new ProductTransformer();
    }
}

