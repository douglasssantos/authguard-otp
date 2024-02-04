<?php

namespace Larakeeps\AuthGuard\Services;

use Closure;
use RuntimeException;
use Throwable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Larakeeps\AuthGuard\Models\AuthGuard;

class AuthGuardService
{
    private $code, $status, $message, $isConfirmed, $number_digits, $access_token, $ip_address;
    private AuthGuard $data;
    public $getHeaders = [];

    public function __construct($code = null)
    {
        $this->code = ($code ?? $this->code);

        $this->getHeaders = collect(array_change_key_case(getallheaders()));

        $authHeader = str_replace(" ", "", $this->getHeaders->get('authorization'));

        if(str_contains(strtolower($authHeader), "bearer")) {
            $this->access_token = substr($authHeader, 6);
        }

        $this->access_token = !empty($this->access_token) ? $this->access_token : null;

        $this->ip_address = request()->ip();

        $this->number_digits = config('authguard-otp.numberDigits');

    }

    public function getAccessToken(){
        return $this->access_token;
    }

    public function getIpAddress(){
        return $this->ip_address;
    }

    public function generateDigits($digits = 6)
    {
        return (int) substr(number_format((time() * mt_rand()), 0, '', ''), 0, $digits);
    }

    public function setNumberDigits($number = null)
    {
        if(!empty($number))
            $this->number_digits = $number;
    }
    public function params(&$model)
    {

        $this->setNumberDigits($model->number_digits);

        $model->access_token = $this->getAccessToken();
        $model->ip_address = $this->getIpAddress();
        $model->code = $this->generateDigits($this->number_digits);
        $model->expires_at = now()->addMinutes(config('authguard-otp.otpExpirationTime'));
        $model->attempts_left = 0;
        $model->confirmed = false;

    }

    private function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    private function setConfirmed(string $status)
    {
        $this->isConfirmed = $status;

        return $this;
    }

    public function isConfirmed()
    {
        return $this->isConfirmed;
    }

    private function setData(AuthGuard $authguard)
    {
        $this->data = $authguard;

        return $this;
    }

    public function getData(): AuthGuard
    {
        return $this->data;
    }

    private function setStatus(bool $status)
    {
        $this->status = $status;

        return $this;
    }

    private function getStatus(): bool
    {
        return $this->status;
    }


    public function get(): Collection
    {
        return collect([
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'authguard' => $this->getData()
        ]);
    }

    public function authGuardValidator($phone, $email = null, $reference = null)
    {
        $authGuard = AuthGuard::where("phone", $phone);

        if(!empty($email))
            $authGuard = $authGuard->where("email", $email);

        if(!empty($reference) && config('authguard-otp.validateUserReference'))
            $authGuard = $authGuard->where("reference", $reference);

        if(config('authguard-otp.validateUserIpAddress'))
            $authGuard = $authGuard->where("ip_address", $this->ip_address);

        if(!empty($this->getAccessToken()) && config('authguard-otp.validateUserAccessToken'))
            $authGuard = $authGuard->where("access_token", $this->getAccessToken());

        return $authGuard;
    }

    public function create($phone, $email = null, $reference = null)
    {

        $authGuard = $this->authGuardValidator($phone, $email, $reference);

        if($authGuard->exists()) {

            return $this->setStatus(false)
                ->setMessage(config("authguard-otp.responses.error.thereIsAlreadyAnActiveCode"))
                ->setData($authGuard->first());

        }

        $authGuard = new AuthGuard;

        $authGuard->reference = $reference;
        $authGuard->phone = $phone;
        $authGuard->email = $email;
        $authGuard->number_digits = $this->number_digits;

        if($authGuard->save()) {

            return $this->setStatus(true)
                ->setMessage(config("authguard-otp.responses.success.created"))
                ->setData($authGuard->first());

        }

        return $this->setStatus(false)
            ->setMessage(config("authguard-otp.responses.error.action"));

    }

    public function setAttempts($phone, $reference = null){

        $authGuard = AuthGuard::where("phone", $phone);

        if(!empty($reference) && config('authguard-otp.validateUserReference'))
            $authGuard = $authGuard->where("reference", $reference);

        if(config('authguard-otp.validateUserIpAddress'))
            $authGuard = $authGuard->where("ip_address", $this->ip_address);

        $authGuard = $authGuard->where('confirmed', false);

        if($authGuard->exists()){

            $authGuard = $authGuard->first();

            if($authGuard->increment('attempts_left')
                && $authGuard->attempts_left >= config('authguard-otp.maxAttempts')) {

                $authGuard->delete();

                return $this->setStatus(false)
                    ->setMessage(config('authguard-otp.responses.error.attemptLimitExceeded'));

            }

            return $this->setStatus(false)
                ->setMessage(config("authguard-otp.responses.error.invalidCode"));

        }

        return false;

    }

    public function confirm($code, $phone, $reference = null)
    {
        $authGuard = $this->authGuardValidator($phone, null, $reference)
            ->where('confirmed', false)
            ->where('code', $code);

        if($authGuard->doesntExist()) {

           if(!$this->setAttempts($phone, $reference)) {

               return $this->setStatus(false)
                   ->setMessage(config('authguard-otp.responses.error.notFoundCode'));

           }

        }

        $authGuard = $authGuard->first();

        if($authGuard->expires_at < now()) {

            if($authGuard->delete())
                return $this->setStatus(false)
                    ->setMessage(config("authguard-otp.responses.error.expiredCode"));

        }

        $authGuard->confirmed = true;

        if($authGuard->save()){

            $this->setConfirmed(true);

            return $this->setStatus(true)
                ->setMessage(config('authguard-otp.responses.success.confirmed'));

        }

        return $this->setStatus(false)
            ->setMessage(config('authguard-otp.responses.error.action'));

    }

    public function deleteCode($code)
    {

        $authGuard = AuthGuard::where('code', $code);

        if($authGuard->doesntExist()) {

                return $this->setStatus(false)
                    ->setMessage(config('authguard-otp.responses.error.notFoundCode'));

        }

        if($authGuard->delete()){

            return $this->setStatus(true)
                ->setMessage(config('authguard-otp.responses.success.deleted'));

        }

        return $this->setStatus(false)
            ->setMessage(config("authguard-otp.responses.error.action"));
        
    }

    public function getByCode($code, $phone = null)
    {

        $authGuard = AuthGuard::where('code', $code);

        if(!empty($phone))
            $authGuard = $authGuard->where("phone", $phone);

        if($authGuard->doesntExist()) {

                return $this->setStatus(false)
                    ->setMessage(config('authguard-otp.responses.error.notFoundCode'));

        }

        return $this->setStatus(true)
            ->setMessage(config('authguard-otp.responses.success.founded'))
            ->setData($authGuard->first());

    }

    public function hasCode($code, $phone = null)
    {

        $authGuard = AuthGuard::where('code', $code);

        if(!empty($phone))
            $authGuard = $authGuard->where("phone", $phone);

        return $authGuard->exists();

    }

    public function getResponse(): Collection
    {
        return collect([
            'number_digits' => $this->number_digits,
            'code' => $this->code,
            'message' => $this->getMessage(),
            'status'  =>  $this->getStatus(),
            'user' => [
                'access_token' => $this->getAccessToken(),
                'ip_address' => $this->getIpAddress()
            ]
        ]);
    }

    public function then(Closure $destination)
    {
        return $destination($this->getData(), $this->getResponse());
    }

}