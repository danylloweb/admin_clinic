<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Campaign WhatsApp Distribution Settings
    |--------------------------------------------------------------------------
    |
    | Configurações para distribuir o envio de mensagens WhatsApp ao longo
    | do dia usando Laravel Queues, evitando restrições da API.
    |
    */

    'send' => [
        // Horário inicial de envio (formato 24h: HH:MM)
        'start_time' => env('CAMPAIGN_SEND_START_TIME', '08:00'),

        // Horário final de envio (formato 24h: HH:MM)
        'end_time' => env('CAMPAIGN_SEND_END_TIME', '20:00'),

        // Delay mínimo entre mensagens (em segundos)
        'min_delay_seconds' => env('CAMPAIGN_SEND_MIN_DELAY_SECONDS', 30),

        // Delay máximo entre mensagens (em segundos)
        'max_delay_seconds' => env('CAMPAIGN_SEND_MAX_DELAY_SECONDS', 120),

        // Ativar randomização de delays (±15 segundos)
        'randomize_delay' => env('CAMPAIGN_SEND_RANDOMIZE_DELAY', true),

        // Amplitude de randomização (em segundos)
        'randomization_amplitude' => env('CAMPAIGN_SEND_RANDOMIZATION_AMPLITUDE', 15),
    ],

    'queue' => [
        // Conexão da fila (redis, database, sync)
        'connection' => env('QUEUE_CONNECTION', 'redis'),

        // Nome da fila
        'name' => env('CAMPAIGN_QUEUE_NAME', 'campaigns'),

        // Número de tentativas antes de falhar
        'tries' => env('CAMPAIGN_JOB_TRIES', 3),

        // Timeout em segundos para cada job
        'timeout' => env('CAMPAIGN_JOB_TIMEOUT', 30),
    ],

];

