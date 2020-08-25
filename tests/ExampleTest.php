<?php

namespace OnzaMe\Helpers\Tests;

use Orchestra\Testbench\TestCase;
use OnzaMe\Helpers\HelpersServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [HelpersServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
