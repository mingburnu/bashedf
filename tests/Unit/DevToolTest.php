<?php

namespace Tests\Unit;

use App\Services\DevToolService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\CircularDependencyException;
use Illuminate\Testing\Assert;
use Tests\TestCase;

class DevToolTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     * @throws BindingResolutionException
     * @throws CircularDependencyException
     */
    public function testBasicTest()
    {
        $size = app(DevToolService::class)->generateIdeHelperRepositoryIndexes();
        Assert::assertGreaterThan(0, $size);
    }
}
