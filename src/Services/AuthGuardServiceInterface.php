<?php

namespace Larakeeps\AuthGuard\Services;

use Illuminate\Support\Collection;
use Larakeeps\AuthGuard\Models\AuthGuard;

interface AuthGuardServiceInterface
{
    public function getAccessToken(): string;
    public function getIpAddress(): string;
    public function isConfirmed(): bool;
    public function getData(): AuthGuard;
    public function getMessage(): string;
    public function getStatus(): bool;
    public function get(): Collection;
    public function create($phone, $email = null, $reference = null): AuthGuard | false;
    public function confirm($code, $phone, $reference = null): bool;
    public function deleteCode($code): bool;
    public function getByCode($code, $phone = null): AuthGuard | false;
    public function hasCode($code, $phone = null): bool;
}