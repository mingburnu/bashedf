<?php

namespace Tests\Feature;

use App\Entities\User;
use Auth;
use Tests\TestCase;

class DepositsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStoreDeposit()
    {
        Auth::guard('user')->loginUsingId(User::whereNotNull(config('auth.guards.api.storage_key'))->latest()->first()->getKey(), true);
        $response = $this->post(route('deposits.store'), ['amount' => '400', 'bank_card_id' => '1000']);
        $response->assertStatus(302);
    }
}
