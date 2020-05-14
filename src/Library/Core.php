<?php

namespace Wmandai\Mpesa\Library;

use GuzzleHttp\ClientInterface;

/**
 * Class Core
 *
 * @package Wmandai\Mpesa\Library
 */
class Core
{
    /**
     * @var ClientInterface
     */
    public $client;
    /**
     * @var Authenticator
     */
    public $auth;

    /**
     * Core constructor.
     *
     * @param  ClientInterface $client
     * @throws \Wmandai\MobileMoney\Mpesa\Exceptions\MpesaException
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->auth = new Authenticator($this);
    }
}
