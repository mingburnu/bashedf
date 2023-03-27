<?php

namespace Tests\Unit;

use App\Repositories\UserRepository;
use App\Services\SysService;
use DB;
use Log;
use Tests\TestCase;
use Throwable;

class WhiteIpsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testInsertIps()
    {
        $merchant_id = '1619511774';
        $ips = ['127.0.0.1', '192.168.1.251'];
        $user_id = app(UserRepository::class)->whereMerchantId($merchant_id)->firstOrFail()->id;

        try {
            DB::beginTransaction();
            foreach ($ips as $ip) {
                var_dump($ip);
                var_dump(app(SysService::class)->insertWhiteIP($user_id, $ip));
            }
            DB::commit();
            $result = true;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $result = false;
        }

        $this->assertTrue($result);
    }

    /**
     * @throws Throwable
     */
    public function testDropIps()
    {
        $merchant_id = '1619511774';
        $ips = ['127.0.0.1', '192.168.1.251'];
        $user_id = app(UserRepository::class)->whereMerchantId($merchant_id)->firstOrFail()->id;

        try {
            DB::beginTransaction();
            foreach ($ips as $ip) {
                var_dump($ip);
                var_dump(app(SysService::class)->dropWhiteIP($user_id, $ip));
            }
            DB::commit();
            $result = true;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $result = false;
        }

        $this->assertTrue($result);
    }
}
