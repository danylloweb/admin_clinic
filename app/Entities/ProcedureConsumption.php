<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class ProcedureConsumption extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $fillable = [
        'product_id', 'product_lot_id', 'patient_id',
        'aesthetic_procedure_evolution_id', 'professional_id',
        'quantity_used', 'consumption_date', 'notes'
    ];

    protected $casts = [
        'quantity_used' => 'integer',
        'consumption_date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(ProductLot::class, 'product_lot_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function aestheticEvolution(): BelongsTo
    {
        return $this->belongsTo(AestheticProcedureEvolution::class, 'aesthetic_procedure_evolution_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}

