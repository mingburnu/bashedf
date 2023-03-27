<?php

namespace Tests\Feature;

use App\Repositories\UserRepository;
use Exception;
use GuzzleHttp\Psr7\MimeType;
use Tests\TestCase;

class TokenTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @throws Exception
     */
    public function testGenerateToken()
    {
        $user = app(UserRepository::class)->whereNotNull(config('auth.guards.api.storage_key'))->find(1);
        $headers = ['Accept' => MimeType::fromExtension('json')];
        $response = $this->withToken($user->api_key)->post(route('api.tokens.store'), [], $headers);
        $response->dump();
        $response->assertStatus(201);
    }
}
