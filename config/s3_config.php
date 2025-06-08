<?php

return [
    'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
    'key'    => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'token'  => env('AWS_SESSION_TOKEN')
];
