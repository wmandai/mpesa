<?php

namespace Wmandai\Mpesa\Repositories;

use Wmandai\Mpesa\Library\RegisterUrl;

/**
 * Class Register
 * @package Wmandai\Mpesa\Repositories
 */
class Register
{
    /**
     * @var RegisterUrl
     */
    private $registra;

    /**
     * Register constructor.
     * @param RegisterUrl $registerUrl
     */
    public function __construct(RegisterUrl $registerUrl)
    {
        $this->registra = $registerUrl;
    }

    /**
     * @return mixed
     * @throws \Wmandai\Mpesa\Exceptions\MpesaException
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doRegister()
    {
        return $this->registra->register(\config('laravel-mpesa.c2b.short_code'))
            ->onConfirmation(\config('laravel-mpesa.c2b.confirmation_url'))
            ->onValidation(\config('laravel-mpesa.c2b.validation_url'))
            ->submit();
    }
}
