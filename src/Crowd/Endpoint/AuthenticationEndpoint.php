<?php

namespace Bluetea\Crowd\Endpoint;

use Bluetea\Api\Endpoint\BaseEndpoint;
use Bluetea\Api\Endpoint\EndpointInterface;
use Bluetea\Api\Request\HttpMethod;

class AuthenticationEndpoint extends BaseEndpoint implements EndpointInterface
{
    /**
     * Authenticate user with Crowd
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public function authentication($username, $password)
    {
        $endpoint = sprintf('authentication?username=%s', urlencode($username));
        $parameters['value'] = $password;
        return $this->apiClient->callEndpoint(
            $endpoint,
            $parameters,
            null,
            HttpMethod::REQUEST_POST
        );
    }
}