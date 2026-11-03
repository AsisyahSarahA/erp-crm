<?php

declare(strict_types=1);

return [
    'core' => [
        'name' => 'core-service',
        'url' => env('CORE_SERVICE_URL', 'http://127.0.0.1:8001'),
        'port' => 8001,
        'timeout' => 10,
    ],
    'crm' => [
        'name' => 'crm-service',
        'url' => env('CRM_SERVICE_URL', 'http://127.0.0.1:8002'),
        'port' => 8002,
        'timeout' => 15,
    ],
    'marketing' => [
        'name' => 'marketing-service',
        'url' => env('MARKETING_SERVICE_URL', 'http://127.0.0.1:8003'),
        'port' => 8003,
        'timeout' => 15,
    ],
    'hrd' => [
        'name' => 'hrd-service',
        'url' => env('HRD_SERVICE_URL', 'http://127.0.0.1:8004'),
        'port' => 8004,
        'timeout' => 10,
    ],
    'pm' => [
        'name' => 'pm-service',
        'url' => env('PM_SERVICE_URL', 'http://127.0.0.1:8005'),
        'port' => 8005,
        'timeout' => 15,
    ],
    'finance' => [
        'name' => 'finance-service',
        'url' => env('FINANCE_SERVICE_URL', 'http://127.0.0.1:8006'),
        'port' => 8006,
        'timeout' => 15,
    ],
    'ai' => [
        'name' => 'ai-service',
        'url' => env('AI_SERVICE_URL', 'http://127.0.0.1:8007'),
        'port' => 8007,
        'timeout' => 60,
    ],
    'orchestrator' => [
        'name' => 'orchestrator-service',
        'url' => env('ORCHESTRATOR_SERVICE_URL', 'http://127.0.0.1:8008'),
        'port' => 8008,
        'timeout' => 15,
    ],
];

