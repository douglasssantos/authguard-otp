<?php

namespace Larakeeps\AuthGuard\Facades;

use Exception;
use Illuminate\Support\Facades\Facade;
use Larakeeps\AuthGuard\Models\AuthGuard;
use Illuminate\Support\Collection;

/**
 * @method static string getAccessToken()
 * @method static string getIpAddress()
 * @method static bool isConfirmed()
 * @method static AuthGuard getData()
 * @method static string getMessage()
 * @method static bool getStatus()
 * @method static Collection get()
 * @method static Collection getResponse()
 * @method static AuthGuard|false create($phone, $email, $reference)
 * @method static bool confirm($code, $phone, $reference)
 * @method static bool deleteCode($code)
 * @method static AuthGuard|false getByCode($code, $phone)
 * @method static bool hasCode($code, $phone)
 * @see \Larakeeps\AuthGuard\Services\AuthGuardServiceInterface
 */

class OTP extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'AuthGuardOtp';
    }
}
