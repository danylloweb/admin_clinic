<?php

namespace App\Services;

use App\Enums\StockMovementTypeEnum;
use App\Entities\StockMovement;

class StockMovementService
{
    public function create(array $data): StockMovement
    {
        return StockMovement::create([
            'product_id' => $data['product_id'],
            'product_lot_id' => $data['product_lot_id'] ?? null,
            'movement_type' => $data['movement_type'],
            'quantity' => $data['quantity'],
            'user_id' => auth()->id(),
            'notes' => $data['notes'] ?? null,
            'metadata' => [
                'timestamp' => now()->toIso8601String(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            'created_at' => now(),
        ]);
    }

    public function getByProduct($productId, $limit = 50)
    {
        return StockMovement::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getByType($type, $limit = 50)
    {
        return StockMovement::where('movement_type', $type)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getConsumptionHistory($productId, $days = 30)
    {
        $startDate = now()->subDays($days);
        return StockMovement::where('product_id', $productId)
            ->where('movement_type', 'consumption')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

