<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\CognitoCallbackRequest;
use App\Services\Auth\JwtVerifier;
use Collective\Annotations\Routing\Annotations\Annotations\Controller;
use Collective\Annotations\Routing\Annotations\Annotations\Get;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(prefix="/auth")
 */
class AuthController extends BaseController
{
    /**
     * @Get("/login", as="login")
     */
    public function login(): Response
    {
        $url = config('aws.cognito.login_endpoint') . '?' . http_build_query([
                'response_type' => 'code',
                'client_id' => config('aws.cognito.client_id'),
                'redirect_uri' => URL::route('auth.callback'),
            ]);

        return redirect($url);
    }

    /**
     * @Get("/callback", as="auth.callback")
     */
    public function callback(CognitoCallbackRequest $request, JwtVerifier $jwtVerifier): Response
    {
        $code = $request->validated()['code'];;

        $url = config('aws.cognito.token_endpoint') . '?' . http_build_query([
                'grant_type' => 'authorization_code',
                'client_id' => config('aws.cognito.client_id'),
                'redirect_uri' => URL::route('auth.callback'),
                'code' => $code,
            ]);

        $response = Http::withBasicAuth(config('aws.cognito.client_id'), config('aws.cognito.client_secret'))
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->post($url);

        if (!$response->successful()) {
            return redirect('/');
        }

        $sub = $jwtVerifier->verify($response->json('id_token', ''));

        return response()->json($sub);
    }

    /**
     * @Get("/logout")
     */
    public function logout(): Response
    {
        return redirect('/');
    }
}
