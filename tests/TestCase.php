<?php

namespace Larakeeps\AuthGuard\Tests;

use Larakeeps\AuthGuard\Services\AuthGuardService;
use Larakeeps\AuthGuard\Providers\AuthGuardServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{

    /**
     * add the package provider
     *
     * @param  Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AuthGuardServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'AuthGuardOTP' => AuthGuardService::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('user', ['username' => 'testcase', 'password' => 'testcase']);
    }
}