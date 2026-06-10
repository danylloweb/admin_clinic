<?php

namespace App\Entities;

use App\Enums\StockMovementTypeEnum;
use MongoDB\Laravel\Eloquent\Model;

class StockMovement extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'stock_movements';

    protected $fillable = [
        'product_id',
        'product_lot_id',
        'movement_type',
        'quantity',
        'user_id',
        'notes',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'product_lot_id' => 'integer',
        'quantity' => 'integer',
        'user_id' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function getMovementTypeLabel(): string
    {
        try {
            return StockMovementTypeEnum::from($this->movement_type)->label();
        } catch (\ValueError) {
            return $this->movement_type;
        }
    }

    public function getMovementTypeBadge(): string
    {
        try {
            return StockMovementTypeEnum::from($this->movement_type)->badgeClass();
        } catch (\ValueError) {
            return 'badge bg-secondary';
        }
    }
}

