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

class CurlClient extends BaseClient implements ClientInterface
{
    /**
     * @var resource
     */
    protected $curl;

    /**
     * @var string
     */
    protected $cookiefile;

    /**
     * @var string
     */
    protected $crtBundleFile;

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
     * This method initializes a CURL instance and saves it in the private $curl variable.
     * Other methods can retrieve this CURL instance by calling $this->getCurl().
     * This method should called only once
     *
     * @throws ApiException
     */
    public function init()
    {
        // Check if the curl object isn't set already
        if ($this->isInit()) {
            throw new ApiException("A Curl instance is already initialized");
        }

        // Initialize Curl
        $ch = curl_init();
        // Set the header
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // Expect returndata
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Set the HTTP version to 1.0
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        // Set cookiefile for sessiondata
        if (!empty($this->cookiefile)) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->getCookiefile());
        }
        // Set crtBundleFile (certificate) if given else disable SSL verification
        if (!empty($this->crtBundleFile)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->getCrtBundleFile());
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        // Enable Curl verbose output if debug mode is enabled
        if ($this->isDebug()) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }
        // Set basic authentication if enabled
        if ($this->authentication instanceof BasicAuthentication) {
            curl_setopt($ch, CURLOPT_USERPWD, sprintf("%s:%s", $this->authentication->getUsername(), $this->authentication->getPassword()));
        }

        // Save Curl object in the class so it can be used by other methods
        $this->setCurl($ch);
    }

    protected function call()
    {
        // Prepare the call
        $ch = $this->prepareCall();
        // Execute the call
        $data = json_decode(curl_exec($ch));
        // Finish the call
        $this->finishCall($ch, $data);
    }

    /**
     * Finishes the call and set the data or throws an exception if the request was not OK
     *
     * @param $ch
     * @param $data
     * @throws ApiException
     * @throws UnauthorizedException
     * @throws ResultException
     * @throws HttpNotFoundException
     */
    protected function finishCall($ch, $data)
    {
        // Reset endpoint parameters
        $this->setEndpointParameters(array());

        // Get error number from curl
        $errorNumber = curl_errno($ch);
        if ($errorNumber > 0) {
            throw new ApiException(sprintf('JIRA REST API failed with error no. %s, "%s"', $errorNumber, curl_error($ch)));
        }

        // Get HTTP status code from Curl
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if not found
        if ($httpStatus === StatusCodes::HTTP_NOT_FOUND) {
            throw new HttpNotFoundException();
        }

        // Check if unauthorized
        if ($httpStatus === StatusCodes::HTTP_UNAUTHORIZED) {
            throw new UnauthorizedException();
        }

        // Check if no content
        if ($httpStatus === StatusCodes::HTTP_NO_CONTENT) {
            throw new ResultException("No content");
        }

        // Check if the HTTP code is OK, else throw exception
        if (!StatusCodes::isError($httpStatus)) {
            // If HTTP code is 2xx, set the data in $this->curlData;
            $this->setData($data);
        } else {
            throw new HttpException(sprintf('Got HTTP status code %s', $httpStatus), $httpStatus);
        }
    }

    /**
     * Prepares the call and sets the endpoint URL
     *
     * @return resource
     */
    protected function prepareCall()
    {
        // Check if the curl object is set
        if (!$this->isInit()) {
            // If it isn't, we do it right now
            $this->init();
        }

        // Get Curl object
        $ch = $this->getCurl();
        // Get endpoint parameters
        $parameters = $this->getEndpointParameters();

        // Check which method to use
        if ($this->getHttpMethod() === HttpMethod::REQUEST_GET) {
            $url = vsprintf('%s/%s?%s', array (
                $this->getBaseurl(),
                $this->getEndpoint(),
                http_build_query($parameters)
            ));
        } else {
            $url = vsprintf('%s/%s', array (
                $this->getBaseurl(),
                $this->getEndpoint()
            ));
            if ($this->getHttpMethod() === HttpMethod::REQUEST_POST) {
                // Set the Curl method to POST
                curl_setopt($ch, CURLOPT_POST, 1);
                // Add the parameters as POST fields
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            } elseif ($this->getHttpMethod() === HttpMethod::REQUEST_PUT) {
                // Set the Curl method to PUT
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                // Add the parameters as POST fields
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            }
        }

        // Set full URL in Curl
        curl_setopt($ch, CURLOPT_URL, $url);

        return $ch;
    }

    /**
     * @param resource $curl
     */
    public function setCurl($curl)
    {
        $this->curl = $curl;
    }

    /**
     * @return resource
     */
    public function getCurl()
    {
        return $this->curl;
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
     * @throws \Bluetea\Api\Exception\ApiException
     */
    public function getResultHttpCode()
    {
        if (!$this->isInit()) {
            throw new ApiException('Curl is not initialized yet!');
        }

        return curl_getinfo($this->getCurl(), CURLINFO_HTTP_CODE);
    }

    /**
     * Returns if Curl is initialized or not
     *
     * @return bool
     */
    protected function isInit()
    {
        if ($this->getCurl() != null) {
            return true;
        }
        return false;
    }

    /**
     * PHP Class destructor
     */
    function __destruct()
    {
        if ($this->isInit()) {
            $ch = $this->getCurl();
            curl_close($ch);
        }
    }
}