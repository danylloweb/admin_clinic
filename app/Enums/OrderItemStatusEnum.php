<?php

namespace App\Enums;

enum OrderItemStatusEnum: int
{
    case PAYMENT_PENDING      = 1; // Pagamento Pendente
    case SCHEDULING_PENDING   = 2; // Agendamento Pendente
    case SCHEDULED            = 3; // Agendado
    case CONFIRMED            = 4; // Confirmado
    case PRE_EXECUTION        = 5; // Pré-execução
    case IN_PROGRESS          = 6; // Em execução
    case COMPLETED            = 7; // Concluído
    case CANCELED             = 8; // Cancelado

    /**
     * Retorna a tradução em português do status.
     *
     * @return string
     */
    public function translate(): string
    {
        return match ($this) {
            self::PAYMENT_PENDING      => 'Pagamento Pendente',
            self::SCHEDULING_PENDING   => 'Agendamento Pendente',
            self::SCHEDULED            => 'Agendado',
            self::CONFIRMED            => 'Confirmado',
            self::PRE_EXECUTION        => 'Pré-execução',
            self::IN_PROGRESS          => 'Em execução',
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
