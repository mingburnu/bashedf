<?php

namespace App\Services;

use Curl;
use GuzzleHttp\Psr7\MimeType;

class CallbackService
{
    public function call(string $url, array $data, int $max_times = 3, int $interval = 30)
    {
        $headers = ['Accept' => MimeType::fromExtension('json')];
        $times = 0;
        while (true) {
            $response = Curl::to($url)->withData($data)->withHeaders($headers)->post();

            $times++;
            if ($response !== false || $times === $max_times) {
                break;
            }

            sleep($interval);
        }

        return $response;
    }
}