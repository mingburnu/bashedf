<?php

namespace Tests\Feature;

use App\Repositories\UserRepository;
use App\Services\SignService;
use GuzzleHttp\Psr7\MimeType;
use Tests\TestCase;

class MerchantTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testQueryWallet()
    {
        $user = app(UserRepository::class)->findOrFail(1);
        $data = [];

        $token = '60|0f8vSQvVCqFjsnoRARMVIzMK9u9lZNay3qgVuMjJ';
        $headers = ['Accept' => MimeType::fromExtension('json')];
        $response = $this->withToken($token)->get(route('api.wallets.index', app(SignService::class)->sign($data, $user->api_key)), $headers);
        $response->dump();
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testQueryPayment()
    {
        $user = app(UserRepository::class)->find(1);
        $data = [
            'field' => 'order_id',
            'order_id' => 'E6GkOQRnzRkMlDxoxDlX907LN',
            'customized_id' => '777'
        ];

        $data = app(SignService::class)->sign($data, $user->api_key);
        $token = '60|0f8vSQvVCqFjsnoRARMVIzMK9u9lZNay3qgVuMjJ';
        $headers = ['Accept' => MimeType::fromExtension('json')];
        $response = $this->withToken($token)->get(route('api.payments.index', $data), $headers);
        $response->dump();
        $response->assertStatus(200);
    }
}
