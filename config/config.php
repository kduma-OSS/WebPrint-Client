<?php

return [
    // API Endpoint for accessing WebPrint Server. Usually ending with: /api/web-print
    'endpoint' => env('WEB_PRINT_ENDPOINT'),

    // API Token generated for your Client Application
    'token' => env('WEB_PRINT_ACCESS_TOKEN'),

    // Printer aliases for easy headless printing
    'printers' => [
        'default' => env('WEB_PRINT_PRINTER_DEFAULT'),
    ],
];
