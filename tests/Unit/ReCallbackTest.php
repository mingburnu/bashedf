<?php

namespace Tests\Unit;

use App\Jobs\CallbackJob;
use App\Presenters\PaymentPresenter;
use App\Repositories\PaymentRepository;
use App\Services\SignService;
use Exception;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Testing\Assert;
use Ixudra\Curl\Facades\Curl;
use Tests\TestCase;

class ReCallbackTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $payment = app(PaymentRepository::class)->findOrFail(1);
        $job = null;
        if (!is_null($payment->callback_url)) {
            $job = CallbackJob::dispatch($payment)->onConnection('database')->onQueue('callback');
        }

        Assert::assertIsObject($job);
    }

    /**
     * @throws Exception
     */
    public function testCurl()
    {
        $customized_id = '210617417636';
        $user_id = 0;
        $payment = app(PaymentRepository::class)->whereUserId($user_id)->whereCustomizedId($customized_id)->first();
        $data = app(PaymentPresenter::class)->present($payment);
        $data = app(SignService::class)->sign($data, $payment->user->api_key);
        $headers = ['Accept' => MimeType::fromExtension('json')];
        $response = Curl::to($payment->callback_url)->withData($data)->withHeaders($headers)->returnResponseArray()->post();
        echo $payment->callback_url . PHP_EOL;
        echo json_encode($data) . PHP_EOL;
        echo $response['content'];

        Assert::assertEquals(200, $response['status']);
    }
}
