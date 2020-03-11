<?php

return [
    'default_currency' => env('BASE_CURRENCY', 'TRY'),
    'default_status' => 'concept',
    'locale' => env('APP_LOCALE', 'tr'),
    'table_names' => [
        'invoices' => 'invoices',
        'invoice_lines' => 'invoice_lines',
    ]
];
