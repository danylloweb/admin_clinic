<?php

namespace App\Enums;

enum ItemTypeEnum: int
{
    case PRODUCT = 1;
    case SERVICE = 2;

    public static function fromString(string $type): self
    {
        return match ($type) {
            'product' => self::PRODUCT,
            'service' => self::SERVICE,
            default => self::PRODUCT,
        };
    }

    public function translate(): string
    {
        return match ($this) {
            self::PRODUCT => 'Produto',
            self::SERVICE => 'Serviço',
        };
    }

    public static function translateFromInt(int $value): ?string
    {
        foreach (self::cases() as $type) {
            if ($type->value === $value) {
                return $type->translate();
            }
        }
        return null;
    }

    public function translateReport(): string
    {
        return match ($this) {
            self::PRODUCT => 'Bundle',
            self::SERVICE => 'A Vulso',
        };
    }
}
