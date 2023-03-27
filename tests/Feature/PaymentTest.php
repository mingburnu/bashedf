<?php

namespace Tests\Feature;

use App\Repositories\UserRepository;
use App\Services\SignService;
use Exception;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Support\Str;
use Tests\TestCase;
use Zhuzhichao\BankCardInfo\BankCard;

class PaymentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @throws Exception
     */
    public function testInsertPrepayment()
    {
        $user = app(UserRepository::class)->whereNotNull(config('auth.guards.api.storage_key'))->find(1);

        $account_number = '6228480086989255670';
        $data = [
            'account_name' => '田光',
            'account_number' => $account_number,
            'bank_name' => BankCard::info($account_number)['bankName'],
            'amount' => '100',
            'branch' => '本行',
            'customized_id' => Str::uuid()->serialize(),
            'url' => 'google.com',
        ];

        $data = app(SignService::class)->sign($data, $user->api_key);
        $token = '60|0f8vSQvVCqFjsnoRARMVIzMK9u9lZNay3qgVuMjJ';
        $headers = ['Accept' => MimeType::fromExtension('json')];
        $response = $this->withToken($token)->post(route('api.payments.store'), $data, $headers);
        $response->dump();
        $response->assertStatus(201);
    }
}
