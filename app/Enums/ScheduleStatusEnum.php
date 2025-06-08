<?php

namespace App\Enums;

enum ScheduleStatusEnum: int
{
    case SCHEDULING_PENDING   = 1; // Agendamento Pendente
    case SCHEDULED            = 2; // Agendado
    case CONFIRMED            = 3; // Confirmado
    case PRE_EXECUTION        = 4; // Pré-execução
    case IN_PROGRESS          = 5; // Em execução
    case COMPLETED            = 6; // Concluído
    case CANCELED             = 7; // Cancelado

    /**
     * Retorna a tradução em português do status.
     *
     * @return string
     */
    public function translate(): string
    {
        return match ($this) {
            self::SCHEDULING_PENDING   => 'Agendamento Pendente',
            self::SCHEDULED            => 'Agendado',
            self::CONFIRMED            => 'Confirmado',
            self::PRE_EXECUTION        => 'Pré-execução',
            self::IN_PROGRESS          => 'Em andamento',
            self::COMPLETED            => 'Concluído',
            self::CANCELED             => 'Cancelado',
        };
    }

    /**
     * @param int $value
     * @return string|null
     */
    public static function translateFromInt(int $value): ?string
    {
        foreach (self::cases() as $status) {
            if ($status->value === $value) {
                return $status->translate();
            }
        }
        return "Error";
    }

    /**
     * @param int $value
     * @return string|null
     */
    public static function getKeyFromStatusId(int $value): ?string
    {
        foreach (self::cases() as $status) {
            if ($status->value === $value) {
                return $status->name;
            }
        }
        return null;
    }
}
