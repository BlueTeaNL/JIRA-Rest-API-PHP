<?php

namespace Bluetea\Api\Client;

use Bluetea\Api\Authentication\BasicAuthentication;
use Bluetea\Api\Exception\ApiException;
use Bluetea\Api\Exception\HttpNotFoundException;
use Bluetea\Api\Exception\ResultException;
use Bluetea\Api\Exception\UnauthorizedException;
use Bluetea\Api\Request\HttpMethod;
use Bluetea\Api\Request\StatusCodes;
use GuzzleHttp\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class GuzzleClient extends BaseClient implements ClientInterface
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $cookiefile;

    /**
     * @var string
     */
    protected $crtBundleFile;

    /**
     * @var int
     */
    protected $httpStatusCode;

    /**
     * Call the Exact Online API with an endpoint
     */
    public function callEndpoint($endpoint, array $endpointParameters = [], $method = HttpMethod::REQUEST_GET)
    {
        // Set the endpoint
        $this->setEndpoint($endpoint);
        // Set the parameters
        $this->setEndpointParameters($endpointParameters);
        // Set the HTTP method
        $this->setHttpMethod($method);
        // Call the endpoint
        $this->call();
        // return the result
        return $this->getResult();
    }

    /**
     * Initialize the JIRA REST API
     * This method initializes a Guzzle Http client instance and saves it in the private $httpClient variable.
     * Other methods can retrieve this Guzzle Http client instance by calling $this->getHttpClient().
     * This method should called only once
     *
     * @throws ApiException
     */
    public function init()
    {
        // Check if the curl object isn't set already
        if ($this->isInit()) {
            throw new ApiException("A Guzzle Http Client instance is already initialized");
        }

        $defaults = array(
            'version' => '1.0'
        );

        // Enable debug if debug is true
        if ($this->isDebug()) {
            $defaults['debug'] = true;
        }

        // Set crtBundleFile (certificate) if given else disable SSL verification
        if (!empty($this->crtBundleFile)) {
            $defaults['verify'] = $this->getCrtBundleFile();
        }

        // Set cookiefile for sessiondata
        if (!empty($this->cookiefile)) {
            $defaults['config'] = array(
                'curl' => array(
                    CURLOPT_COOKIEJAR => $this->getCookiefile()
                )
            );
        }

        $httpClient = new Client(array(
            'base_url' => $this->getBaseUrl(), 'defaults' => $defaults
        ));

        $this->setHttpClient($httpClient);
    }

    /**
     * Call the API endpoint
     *
     * @throws \Bluetea\Api\Exception\UnauthorizedException
     * @throws \Bluetea\Api\Exception\HttpNotFoundException
     * @throws \Bluetea\Api\Exception\HttpException
     * @throws \Bluetea\Api\Exception\ResultException
     */
    protected function call()
    {
        // Check if the curl object is set
        if (!$this->isInit()) {
            // If it isn't, we do it right now
            $this->init();
        }

        $options = array();
        // Set basic authentication if enabled
        if ($this->authentication instanceof BasicAuthentication) {
            $options['auth'] = array(
                $this->authentication->getUsername(),
                $this->authentication->getPassword()
            );
        }

        $request = $this->getHttpClient()->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            array_merge(
                $options,
                array('headers' => $this->getHeaders())
            )
        );

        foreach ($this->getEndpointParameters() as $key => $value) {
            $request->getQuery()->set($key, $value);
        }

        try {
            $response = $this->getHttpClient()->send($request);
        } catch (ClientException $e) {// Check if not found
            if ($e->getResponse()->getStatusCode() === StatusCodes::HTTP_NOT_FOUND) {
                throw new HttpNotFoundException();
            }

            // Check if unauthorized
            if ($e->getResponse()->getStatusCode() === StatusCodes::HTTP_UNAUTHORIZED) {
                throw new UnauthorizedException();
            }

            throw $e;
        }

        $this->setHttpStatusCode($response->getStatusCode());

        if ($this->getAccept() == 'application/json') {
            $this->setData($response->json());
        } else {
            $this->setData($response->getBody(true));
        }
    }

    /**
     * @param Client $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param string $crtBundleFile
     */
    public function setCrtBundleFile($crtBundleFile)
    {
        $this->crtBundleFile = $crtBundleFile;
    }

    /**
     * @return string
     */
    public function getCrtBundleFile()
    {
        return $this->crtBundleFile;
    }

    /**
     * @param string $cookiefile
     */
    public function setCookiefile($cookiefile)
    {
        $this->cookiefile = $cookiefile;
    }

    /**
     * @return string
     */
    public function getCookiefile()
    {
        return $this->cookiefile;
    }

    /**
     * @return int
     */
    public function getResultHttpCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @param int $httpStatusCode
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * Returns if Curl is initialized or not
     *
     * @return bool
     */
    protected function isInit()
    {
        if ($this->getHttpClient() != null) {
            return true;
        }
        return false;
    }
}