<?php

namespace App\Entities;

use App\Enums\ProductLotStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class ProductLot extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $fillable = [
        'product_id', 'batch_number', 'manufacture_date', 'expiration_date',
        'quantity_received', 'quantity_available', 'received_date',
        'supplier_id', 'invoice_number', 'status', 'notes'
    ];

    protected $casts = [
        'manufacture_date' => 'date',
        'expiration_date' => 'date',
        'received_date' => 'date',
        'quantity_received' => 'integer',
        'quantity_available' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(ProcedureConsumption::class, 'product_lot_id');
    }

    public function updateStatus(): void
    {
        $today = now()->startOfDay();
        $expirationDate = $this->expiration_date;

        if (!$expirationDate) {
            $this->status = 'normal';
            return;
        }

        if ($today->gt($expirationDate)) {
            $this->status = 'expired';
        } elseif ($today->diffInDays($expirationDate) <= 30) {
            $this->status = 'near_expiration';
        } elseif ($this->quantity_available <= 0) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'normal';
        }
    }

    public function getStatusLabelAttribute(): string
    {
        try {
            return ProductLotStatusEnum::from($this->status)->label();
        } catch (\ValueError) {
            return $this->status;
        }
    }

    public function getStatusBadgeAttribute(): string
    {
        try {
            return ProductLotStatusEnum::from($this->status)->badgeClass();
        } catch (\ValueError) {
            return 'badge bg-secondary';
        }
    }

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expiration_date) {
            return null;
        }
        $days = now()->startOfDay()->diffInDays($this->expiration_date, false);
        return max(-999, $days);
    }
}

