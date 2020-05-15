<?php

namespace Wmandai\Mpesa\Library;

use GuzzleHttp\ClientInterface;
use Wmandai\Mpesa\Library\Authenticator;

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
     * @throws \Wmandai\Mpesa\Exceptions\MpesaException
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->auth = new Authenticator($this);
    }
}
