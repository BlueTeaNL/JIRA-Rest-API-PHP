<?php

namespace Bluetea\Api\Client;

use Bluetea\Api\Authentication\BasicAuthentication;
use Bluetea\Api\Exception\ApiException;
use Bluetea\Api\Exception\HttpException;
use Bluetea\Api\Exception\HttpNotFoundException;
use Bluetea\Api\Exception\ResultException;
use Bluetea\Api\Exception\UnauthorizedException;
use Bluetea\Api\Request\HttpMethod;
use Bluetea\Api\Request\StatusCodes;
use Guzzle\Http\Client;

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

        $httpClient = new Client($this->getBaseUrl(), array(
            'defaults' => $defaults
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
            null,
            ($this->getHttpMethod() == 'GET' ?
                array('query' => $this->getEndpointParameters())
                :
                array('json' => $this->getEndpointParameters())
            ),
            $options
        );

        $response = $request->send();

        $this->setHttpStatusCode($response->getStatusCode());

        // Check if not found
        if ($response->getStatusCode() === StatusCodes::HTTP_NOT_FOUND) {
            throw new HttpNotFoundException();
        }

        // Check if unauthorized
        if ($response->getStatusCode() === StatusCodes::HTTP_UNAUTHORIZED) {
            throw new UnauthorizedException();
        }

        // Check if no content
        if ($response->getStatusCode() === StatusCodes::HTTP_NO_CONTENT) {
            throw new ResultException("No content");
        }

        if ($response->isSuccessful()) {
            $this->setData($response->json());
        } else {
            throw new HttpException(sprintf('Got HTTP status code %s', $response->getStatusCode()), $response->getStatusCode());
        }
    }

    /**
     * @param \Guzzle\Http\Client $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return \Guzzle\Http\Client
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