<?php

namespace Larakeeps\AuthGuard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Larakeeps\AuthGuard\Services\AuthGuardService;

class AuthGuard extends Model
{
    use HasFactory;

    protected $table = "auth_guard_otp_codes";

    protected $guarded = ['id'];

    protected $hidden = ['id', "created_at", "updated_at"];

    protected static function boot ()
    {
        parent::boot();
        static::creating(fn (AuthGuard $authGuard) => (new AuthGuardService)->params($authGuard));
    }



}
