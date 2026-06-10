<?php

namespace App\Enums;

enum ProductCategoryTypeEnum: string
{
    case COSMETIC = 'cosmetic';
    case DERMOCOSMETIC = 'dermocosmetic';
    case MEDICINE = 'medicine';
    case BOTULINUM_TOXIN = 'botulinum_toxin';
    case FILLER = 'filler';
    case BIOSTIMULATOR = 'biostimulator';
    case ENZYME = 'enzyme';
    case EQUIPMENT = 'equipment';
    case DISPOSABLE_MATERIAL = 'disposable_material';
    case CONSUMABLE_MATERIAL = 'consumable_material';
    case INPUT = 'input';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::COSMETIC => 'Cosmético',
            self::DERMOCOSMETIC => 'Dermocosmético',
            self::MEDICINE => 'Medicamento',
            self::BOTULINUM_TOXIN => 'Toxina Botulínica',
            self::FILLER => 'Preenchedor',
            self::BIOSTIMULATOR => 'Bioestimulador',
            self::ENZYME => 'Enzimas',
            self::EQUIPMENT => 'Equipamento',
            self::DISPOSABLE_MATERIAL => 'Material Descartável',
            self::CONSUMABLE_MATERIAL => 'Material de Consumo',
            self::INPUT => 'Insumo',
            self::OTHER => 'Outro',
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

