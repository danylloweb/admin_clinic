<?php

namespace App\Presenters;

use App\Transformers\PatientMedicalRecordTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PatientMedicalRecordPresenter.
 *
 * @package namespace App\Presenters;
 */
class PatientMedicalRecordPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PatientMedicalRecordTransformer();
    }
}
