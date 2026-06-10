<?php

namespace App\Enums;

enum UnitMeasureEnum: string
{
    case UNIT = 'unit';
    case BOX = 'box';
    case AMPULE = 'ampule';
    case FLASK = 'flask';
    case SYRINGE = 'syringe';
    case ML = 'ml';
    case MG = 'mg';
    case G = 'g';
    case KG = 'kg';

    public function label(): string
    {
        return match ($this) {
            self::UNIT => 'Unidade',
            self::BOX => 'Caixa',
            self::AMPULE => 'Ampola',
            self::FLASK => 'Frasco',
            self::SYRINGE => 'Seringa',
            self::ML => 'ml',
            self::MG => 'mg',
            self::G => 'g',
            self::KG => 'kg',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}

