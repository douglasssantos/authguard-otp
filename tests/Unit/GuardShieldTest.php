<?php

namespace Larakeeps\AuthGuard\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Larakeeps\AuthGuard\Models\AuthGuard;
use Larakeeps\AuthGuard\Tests\TestCase;

class AuthGuardOTPTest extends TestCase
{
    public function testIfThereIsAnyCodeCreated()
    {
        $configAuthGuard = Config::get("authguard");
    }

    public function testIfTheCodeIsConfirming()
    {
        $configAuthGuard = Config::get("authguard");
    }

    public function testIfTheCodeIsExpiring()
    {
        $configAuthGuard = Config::get("authguard");
    }

    public function testIfTheCodeIsInvaliding()
    {
        $configAuthGuard = Config::get("authguard");
    }
}