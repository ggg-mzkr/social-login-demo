<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;
use Jose\Component\Core\JWKSet;
use Jose\Easy\JWT;
use Jose\Easy\Load;
use RuntimeException;

class JwtVerifier
{
    public function __construct(
        protected string $keyLocation,
        protected string $aud,
        protected string $iss
    ) {}

    /**
     * @param string $idToken
     * @return array
     */
    public function verify(string $idToken): array
    {
        $keySet = JWKSet::createFromKeyData($this->fetchKeys());
        /** @var JWT $token */
        $token = Load::jws($idToken)->keyset($keySet)->run();

        if (!$this->verifyClaim($token->claims->aud(), $token->claims->iss())) {
            throw new RuntimeException('Invalid claims');
        }

        return $token->claims->all();
    }

    protected function fetchKeys(): array
    {
        return Http::get($this->keyLocation)->json();
    }

    /**
     * JWT クレームを検証する.
     *
     * @param string $aud
     * @param string $iss
     * @return bool
     */
    private function verifyClaim(string $aud, string $iss): bool
    {
        return $this->aud == $aud && $this->iss ==$iss;
    }
}
