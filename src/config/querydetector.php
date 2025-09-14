<?php

return [
    'enabled' => env('QUERY_DETECTOR_ENABLED', true), // força ligado p/ testar
    'threshold' => (int) env('QUERY_DETECTOR_THRESHOLD', 1),

    'except' => [
        // vazio p/ garantir que nada será ignorado
    ],

    'log_channel' => env('QUERY_DETECTOR_LOG_CHANNEL', 'daily'),

    'output' => [
        \BeyondCode\QueryDetector\Outputs\Json::class, // <-- escreve no body JSON
        \BeyondCode\QueryDetector\Outputs\Log::class,  // <-- e em storage/logs
    ],
];
