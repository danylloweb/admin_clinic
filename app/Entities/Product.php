<?php

namespace App\Entities;

use App\Enums\ProductCategoryTypeEnum;
use App\Enums\UnitMeasureEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Product extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $fillable = [
        'internal_code', 'ean_code', 'name', 'trade_name', 'description',
        'image_url',
        'category_type', 'subcategory', 'brand', 'anvisa_registration', 'anvisa_process',
        'requires_batch_tracking', 'requires_expiration_tracking', 'requires_refrigeration',
        'is_injectable', 'requires_patient_tracking', 'unit_measure',
        'minimum_stock', 'ideal_stock', 'current_stock', 'reserved_stock',
        'storage_location', 'aisle', 'cabinet', 'shelf',
        'min_temperature', 'max_temperature', 'ideal_humidity',
        'supplier_id', 'invoice_number', 'purchase_date', 'receipt_date',
        'unit_value', 'sale_value', 'profit_margin', 'status',
        'created_by', 'updated_by', 'change_log'
    ];

    protected $casts = [
        'requires_batch_tracking' => 'boolean',
        'requires_expiration_tracking' => 'boolean',
        'requires_refrigeration' => 'boolean',
        'is_injectable' => 'boolean',
        'requires_patient_tracking' => 'boolean',
        'status' => 'boolean',
        'minimum_stock' => 'integer',
        'ideal_stock' => 'integer',
        'current_stock' => 'integer',
        'reserved_stock' => 'integer',
        'ideal_humidity' => 'integer',
        'purchase_date' => 'date',
        'receipt_date' => 'date',
        'unit_value' => 'decimal:2',
        'sale_value' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'change_log' => 'json',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(ProductLot::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProductDocument::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(ProcedureConsumption::class);
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->current_stock - $this->reserved_stock);
    }

    public function getCategoryLabelAttribute(): string
    {
        try {
            return ProductCategoryTypeEnum::from($this->category_type)->label();
        } catch (\ValueError) {
            return $this->category_type;
        }
    }

    public function getUnitLabelAttribute(): string
    {
        try {
            return UnitMeasureEnum::from($this->unit_measure)->label();
        } catch (\ValueError) {
            return $this->unit_measure;
        }
    }

    public function recordChange(string $field, mixed $oldValue, mixed $newValue, string $userId): void
    {
        $log = $this->change_log ?? [];
        $log[] = [
            'timestamp' => now()->toIso8601String(),
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'user_id' => $userId,
        ];
        $this->change_log = $log;
    }
}

