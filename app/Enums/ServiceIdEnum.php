<?php

namespace App\Enums;
/**
 * ServiceIdEnum
 */
enum ServiceIdEnum: string {
    case MOUNTING      = '114259443';
    case WATERPROOFING = '102610509';
    case CLEANING      = '115461824';

    /**
     * @return string
     */
    public function getTranslation(): string {
        return match($this) {
            self::MOUNTING      => 'Montagem',
            self::WATERPROOFING => 'Impermeabilização',
            self::CLEANING      => 'Limpeza',
        };
    }

    /**
     * @param string $name
     * @return string|null
     */
    public static function fromName(string $name): ?string
    {
        return match(strtolower($name)) {
            'mounting'       => self::MOUNTING->value,
            'waterproofing'  => self::WATERPROOFING->value,
            'cleaning'       => self::CLEANING->value,
            default          => self::MOUNTING->value,
        };
    }

    /**
     * @param string $refId
     * @return string
     */
    public static function getTranslationByRefId(string $refId): string
    {
        foreach (self::cases() as $case) {
            if ($case->value === $refId) {
                return $case->getTranslation();
            }
        }

        return 'Serviço Desconhecido';
    }
}
