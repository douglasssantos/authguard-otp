<?php

namespace Larakeeps\AuthGuard\Facades;

use Exception;
use Illuminate\Support\Facades\Facade;
use Larakeeps\AuthGuard\Models\AuthGuard;
use Illuminate\Support\Collection;
use Larakeeps\AuthGuard\Services\AuthGuardService;

/**
 * @method static string getAccessToken()
 * @method static string getIpAddress()
 * @method static bool isConfirmed()
 * @method static AuthGuard getData()
 * @method static string getMessage()
 * @method static bool getStatus()
 * @method static Collection get()
 * @method static Collection getResponse()
 * @method static AuthGuardService create($phone, $email, $reference)
 * @method static AuthGuardService confirm($code, $phone, $reference)
 * @method static AuthGuardService deleteCode($code)
 * @method static AuthGuardService getByCode($code, $phone)
 * @method static bool hasCode($code, $phone)
 * @see \Larakeeps\AuthGuard\Services\AuthGuardServiceInterface
 */

class OTP extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'AuthGuard';
    }
}
