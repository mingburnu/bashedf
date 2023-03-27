<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class TokenTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetJunkToken()
    {
        PersonalAccessToken::where(Model::CREATED_AT, '<', now()->subMinutes(config('sanctum.expiration')))->get()->each(fn(PersonalAccessToken $token) => var_dump($token->getKey()));
        $this->assertTrue(true);
    }
}
