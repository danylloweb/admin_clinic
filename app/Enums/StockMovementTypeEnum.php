<?php

namespace App\Enums;

enum StockMovementTypeEnum: string
{
    case ENTRY = 'entry';
    case EXIT = 'exit';
    case ADJUSTMENT = 'adjustment';
    case TRANSFER = 'transfer';
    case LOSS = 'loss';
    case EXPIRATION = 'expiration';
    case CONSUMPTION = 'consumption';

    public function label(): string
    {
        return match ($this) {
            self::ENTRY => 'Entrada',
            self::EXIT => 'Saída',
            self::ADJUSTMENT => 'Ajuste',
            self::TRANSFER => 'Transferência',
            self::LOSS => 'Perda',
            self::EXPIRATION => 'Vencimento',
            self::CONSUMPTION => 'Consumo em Procedimento',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ENTRY => 'badge bg-success',
            self::EXIT => 'badge bg-danger',
            self::ADJUSTMENT => 'badge bg-info',
            self::TRANSFER => 'badge bg-secondary',
            self::LOSS => 'badge bg-warning',
            self::EXPIRATION => 'badge bg-danger',
            self::CONSUMPTION => 'badge bg-primary',
        };
    }
}

