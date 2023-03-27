<?php

namespace Tests\Unit;

use DB;
use Tests\TestCase;

class UnionCountTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $parameter1 = 1;
        $parameter2 = $parameter1;

        $result = DB::select(
            DB::raw('select ((select count(*) from deposits where user_id = :parameter1 ) + (select count(*) from payments where user_id = :parameter2 )) as total'),
            compact('parameter1', 'parameter2')
        );
        var_dump($result);

        $this->assertTrue(true);
    }
}
