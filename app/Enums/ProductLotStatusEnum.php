<?php

namespace App\Enums;

enum ProductLotStatusEnum: string
{
    case NORMAL = 'normal';
    case NEAR_EXPIRATION = 'near_expiration';
    case EXPIRED = 'expired';
    case LOW_STOCK = 'low_stock';

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Normal',
            self::NEAR_EXPIRATION => 'Próximo do Vencimento',
            self::EXPIRED => 'Vencido',
            self::LOW_STOCK => 'Estoque Baixo',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NORMAL => 'badge bg-success',
            self::NEAR_EXPIRATION => 'badge bg-warning',
            self::EXPIRED => 'badge bg-danger',
            self::LOW_STOCK => 'badge bg-info',
        };
    }
}

