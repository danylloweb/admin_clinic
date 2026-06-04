<?php

return [
    'allowed_origins' => array_values(array_filter(array_map(
        static fn ($origin) => trim((string) $origin),
        explode(',', (string) env('GIFT_ALLOWED_ORIGINS', 'https://renovarestetica.com.br'))
    ))),

    'throttle_per_minute' => (int) env('GIFT_THROTTLE_PER_MINUTE', 10),
];

