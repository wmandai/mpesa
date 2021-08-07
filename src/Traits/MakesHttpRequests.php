<?php

namespace Wmandai\Mpesa\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait MakesHttpRequests
{
    /**
     * Send GET request
     *
     * @param string $uri
     * @param array $queryParams
     * @param array $headers
     * @return void
     */
    protected function get(string $uri, array $queryParams = [], array $headers = [])
    {
        return $this->request('GET', $uri, $queryParams, [], $headers);
    }
    /**
     * Send POST request to API
     *
     * @param string $uri
     * @param array $formParams
     * @param array $headers
     * @return void
     */
    protected function post(string $uri, array $formParams = [], array $headers = [])
    {
        return $this->request('POST', $uri, [], $formParams, $headers);
    }

    protected function put(string $uri, array $payload = [])
    {
        return $this->request('PUT', $uri, $payload);
    }

    protected function delete(string $uri, array $payload = [])
    {
        return $this->request('DELETE', $uri, $payload);
    }
    /**
     * Send request to Daraja API
     *
     * @param string $method
     * @param string $uri
     * @param array $queryParams
     * @param array $formParams
     * @param array $headers
     */
    protected function request(string $method, string $uri, $queryParams = [], $formParams = [], $headers = [])
    {
        try {
            $response = (new Client(['http_errors' => false]))->request(
                $method,
                $uri,
                [
                    'query' => $queryParams,
                    'json' => $formParams,
                    'headers' => $headers,
                ]
            );
            if (!$this->isSuccessful($response)) {
                return json_decode($response->getBody()->getContents());
            }
            return json_decode($response->getBody()->getContents());
        } catch (ClientException $exception) {
            return json_decode((string) $exception->getResponse()->getBody());
        }
    }
    /**
     * Check if request is successful
     *
     * @param $response
     * @return boolean
     */
    public function isSuccessful($response): bool
    {
        if (!$response) {
            return false;
        }

        return (int) substr($response->getStatusCode(), 0, 1) === 2;
    }
}
