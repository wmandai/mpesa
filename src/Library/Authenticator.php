<?php

namespace Wmandai\Mpesa\Library;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;
use Wmandai\Mpesa\Exceptions\MpesaException;
use Wmandai\Mpesa\Repositories\EndpointsRepository;

/**
 * Class Authenticator
 *
 * @package Wmandai\Mpesa\Library
 */
class Authenticator
{

    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var Core
     */
    protected $engine;
    /**
     * @var Authenticator
     */
    protected static $instance;
    /**
     * @var bool
     */
    public $alt = false;
    /**
     * @var string
     */
    public $credentials;

    /**
     * Authenticator constructor.
     *
     * @param  Core $core
     *
     * @throws MpesaException
     */
    public function __construct(Core $core)
    {
        $this->engine = $core;
        self::$instance = $this;
    }

    /**
     * @param bool $bulk
     *
     * @return string
     * @throws MpesaException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authenticate($bulk = false): ?string
    {
        if ($bulk) {
            $this->alt = true;
        }
        $this->generateCredentials();
        if (config('mpesa.cache_credentials', false) && !empty($key = $this->getFromCache())) {
            return $key;
        }
        try {
            $response = $this->makeRequest();
            if ($response->getStatusCode() === 200) {
                $body = \json_decode($response->getBody());
                $this->saveCredentials($body);
                return $body->access_token;
            }
            throw new MpesaException($response->getReasonPhrase());
        } catch (RequestException $exception) {
            $message = $exception->getResponse() ?
            $exception->getResponse()->getReasonPhrase() :
            $exception->getMessage();

            throw $this->generateException($message);
        }
    }

    /**
     * @param $reason
     *
     * @return MpesaException
     */
    public function generateException($reason): ?MpesaException
    {
        switch (\strtolower($reason)) {
            case 'bad request: invalid credentials':
                return new MpesaException('Invalid consumer key and secret combination');
            default:
                return new MpesaException($reason);
        }
    }

    /**
     * @return $this
     */
    public function generateCredentials(): self
    {
        $key = \config('mpesa.c2b.consumer_key');
        $secret = \config('mpesa.c2b.consumer_secret');
        if ($this->alt) {
            //lazy way to switch to a different app in case of bulk
            $key = \config('mpesa.b2c.consumer_key');
            $secret = \config('mpesa.b2c.consumer_secret');
        }
        $this->credentials = \base64_encode($key . ':' . $secret);
        return $this;
    }

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeRequest(): ResponseInterface
    {
        $this->endpoint = EndpointsRepository::build('auth');
        return $this->engine->client->request(
            'GET',
            $this->endpoint,
            [
                'headers' => [
                    'Authorization' => 'Basic ' . $this->credentials,
                    'Content-Type' => 'application/json',
                ],
            ]
        );
    }

    /**
     * @return mixed
     */
    public function getFromCache()
    {
        return Cache::get($this->credentials);
    }

    /**
     * Store the credentials in the cache.
     *
     * @param $credentials
     */
    public function saveCredentials($credentials)
    {
        Cache::put($this->credentials, $credentials->access_token, 30);
    }
}
