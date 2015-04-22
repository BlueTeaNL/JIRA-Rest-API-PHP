<?php

namespace Bluetea\Api\Client;

use Bluetea\Api\Authentication\AuthenticationInterface;
use Bluetea\Api\Request\HttpMethod;

interface ClientInterface
{
    /**
     * Call the endpoint
     *
     * @param $endpoint
     * @param array $endpointParameters
     * @param string $body
     * @param string $method
     * @return mixed
     */
    public function callEndpoint($endpoint, array $endpointParameters = [], $body = null, $method = HttpMethod::REQUEST_GET);

    /**
     * @param AuthenticationInterface $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication);

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl);

    /**
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod);

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @return mixed
     */
    public function getResultHttpCode();
}