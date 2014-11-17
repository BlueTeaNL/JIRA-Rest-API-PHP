<?php

namespace Bluetea\Api\Client;

use Bluetea\Api\Authentication\AuthenticationInterface;
use Bluetea\Api\Request\HttpMethod;

abstract class BaseClient
{
    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var array
     */
    protected $endpointParameters = array();

    /**
     * @var string
     */
    protected $httpMethod = HttpMethod::REQUEST_GET;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param $baseUrl
     * @param AuthenticationInterface $authentication
     * @param bool $debug
     */
    public function __construct($baseUrl, AuthenticationInterface $authentication, $debug = false)
    {
        $this->setBaseUrl($baseUrl);
        $this->setAuthentication($authentication);
        $this->setDebug($debug);
    }

    /**
     * @param \Bluetea\Api\Authentication\AuthenticationInterface $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return \Bluetea\Api\Authentication\AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param array $endpointParameters
     */
    public function setEndpointParameters(array $endpointParameters)
    {
        $this->endpointParameters = $endpointParameters;
    }

    /**
     * @return array
     */
    public function getEndpointParameters()
    {
        return $this->endpointParameters;
    }

    /**
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->data;
    }
} 