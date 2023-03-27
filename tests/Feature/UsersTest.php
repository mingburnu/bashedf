<?php

namespace Tests\Feature;

use App\Entities\Admin;
use Auth;
use Str;
use Tests\TestCase;

class UsersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStoreMerchant()
    {
        Auth::guard('admin')->loginUsingId(Admin::first()->id, true);
        $name = Str::random(8);
        $email = Str::random(8) . '@test.com';
        $company = Str::random(8);
        $phone = '0123456789';
        $password = 'Pa55w0rd';
        $password_confirmation = 'Pa55w0rd';
        $min_deposit_amount = 1;
        $max_deposit_amount = 10000;
        $min_payment_amount = 1;
        $max_payment_amount = 10000;
        $deposit_processing_fee_percent = 2;
        $payment_processing_fee = 1;
        $bank_cards = [500000];

        $response = $this->post(route('admin.users.store'), compact('name', 'email', 'company', 'phone', 'password', 'password_confirmation', 'min_deposit_amount', 'max_deposit_amount', 'min_payment_amount', 'max_payment_amount', 'deposit_processing_fee_percent', 'payment_processing_fee', 'bank_cards'));
        $response->assertStatus(302);
    }
}