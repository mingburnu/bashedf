<?php

namespace Tests\Unit;

use App\Services\WalletService;
use DB;
use Log;
use Tests\TestCase;
use Throwable;

class OperateWalletTest extends TestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function testBasicTest()
    {
        try {
            DB::beginTransaction();
            app(WalletService::class)->updateBalance(100, '100');
            $isNormal = true;
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            echo $e->getMessage();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $isNormal = false;
        }
        $this->assertTrue($isNormal);
    }
}
