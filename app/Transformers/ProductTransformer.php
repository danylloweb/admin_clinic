<?php

namespace App\Transformers;

use App\Entities\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $model)
    {
        return [
            'id' => (int) $model->id,
            'internal_code' => $model->internal_code,
            'ean_code' => $model->ean_code,
            'name' => $model->name,
            'trade_name' => $model->trade_name,
            'image_url' => $model->image_url,
            'category_type' => $model->category_type,
            'category_label' => $model->category_label,
            'brand' => $model->brand,
            'anvisa_registration' => $model->anvisa_registration,
            'current_stock' => (int) $model->current_stock,
            'available_stock' => (int) $model->available_stock,
            'unit_measure' => $model->unit_measure,
            'unit_label' => $model->unit_label,
            'status' => (bool) $model->status,
            'requires_patient_tracking' => (bool) $model->requires_patient_tracking,
            'created_at' => optional($model->created_at)->toIso8601String(),
            'updated_at' => optional($model->updated_at)->toIso8601String(),
        ];
    }
}

