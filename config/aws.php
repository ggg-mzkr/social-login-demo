<?php

return [
    'region' => env('AWS_DEFAULT_REGION'),
    'cognito' => [
        'domain' => env('AWS_COGNITO_DOMAIN'),
        'pool_id' => env('AWS_COGNITO_POOL_ID'),
        'client_id' => env('AWS_COGNITO_CLIENT_ID'),
        'client_secret' => env('AWS_COGNITO_CLIENT_SECRET'),
        'login_endpoint' => 'https://' . env('AWS_COGNITO_DOMAIN') .'.auth.ap-northeast-1.amazoncognito.com/login',
        'token_endpoint' => 'https://' . env('AWS_COGNITO_DOMAIN') .'.auth.ap-northeast-1.amazoncognito.com/oauth2/token',
    ],
];
