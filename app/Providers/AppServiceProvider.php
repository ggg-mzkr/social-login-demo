<?php

namespace App\Providers;

use App\Services\Auth\JwtVerifier;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(JwtVerifier::class, function ($app) {

            $keyLocation = sprintf(
                'https://cognito-idp.%s.amazonaws.com/%s/.well-known/jwks.json',
                config('aws.region'),
                config('aws.cognito.pool_id'),
            );

            $iss = sprintf(
                'https://cognito-idp.%s.amazonaws.com/%s',
                config('aws.region'),
                config('aws.cognito.pool_id'),
            );

            $aud = config('aws.cognito.client_id');

            return new JwtVerifier($keyLocation, $aud, $iss);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
