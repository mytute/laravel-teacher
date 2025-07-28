<?php

return [
    'secret'   => env('JWT_SECRET'),
    'issuer'   => env('JWT_ISSUER', config('app.url')),
    'audience' => explode(',', env('JWT_AUDIENCE', 'fts')),
    'ttl'      => (int) env('JWT_TTL', 900), // seconds
    'algo'     => 'HS256',
];
