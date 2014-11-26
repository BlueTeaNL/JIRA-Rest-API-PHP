<?php

namespace Bluetea\Crowd\Endpoint;

use Bluetea\Api\Endpoint\BaseEndpoint;
use Bluetea\Api\Endpoint\EndpointInterface;
use Bluetea\Api\Request\HttpMethod;

class UserEndpoint extends BaseEndpoint implements EndpointInterface
{
    /**
     * Find user
     *
     * @param string $username
     * @param bool $expand
     * @return mixed
     */
    public function find($username, $expand = true)
    {
        $parameters['username'] = $username;
        $parameters['expand'] = $expand;
        return $this->apiClient->callEndpoint('user', $parameters);
    }

    /**
     * Create new user
     *
     * @param $firstName
     * @param $lastName
     * @param $username
     * @param $emailAddress
     * @param bool $active
     * @return mixed
     */
    public function create($firstName, $lastName, $username, $emailAddress, $active = true)
    {
        $parameters = array(
            'first-name' => $firstName,
            'last-name' => $lastName,
            'display-name' => $username,
            'email' => $emailAddress,
            'active' => $active
        );
        return $this->apiClient->callEndpoint('user', $parameters, HttpMethod::REQUEST_POST);
    }

    /**
     * Create new user
     *
     * @param $username
     * @param $firstName
     * @param $lastName
     * @param $newUsername
     * @param $emailAddress
     * @param bool $active
     * @return mixed
     */
    public function update($username, $firstName, $lastName, $newUsername, $emailAddress, $active = true)
    {
        $parameters = array(
            'first-name' => $firstName,
            'last-name' => $lastName,
            'display-name' => $newUsername,
            'email' => $emailAddress,
            'active' => $active
        );
        return $this->apiClient->callEndpoint(sprintf('user?username=%s', $username), $parameters, HttpMethod::REQUEST_PUT);
    }

    /**
     * Delete user
     *
     * @param string $username
     * @return mixed
     */
    public function delete($username)
    {
        $parameters['username'] = $username;
        return $this->apiClient->callEndpoint('user', $parameters, HttpMethod::REQUEST_DELETE);
    }

    /**
     * Update Password
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public function updatePassword($username, $password)
    {
        $parameters['password'] = $password;
        return $this->apiClient->callEndpoint(sprintf('user?username=%s', $username), $parameters, HttpMethod::REQUEST_PUT);
    }

    /**
     * Update Password
     *
     * @param $username
     * @return mixed
     */
    public function mailPasswordResetLink($username)
    {
        return $this->apiClient->callEndpoint(sprintf('user/mail/password?username=%s', $username), array(), HttpMethod::REQUEST_POST);
    }

    /**
     * Get direct user groups
     *
     * @param $username
     * @return mixed
     */
    public function getDirectGroups($username)
    {
        return $this->apiClient->callEndpoint(sprintf('user/group/direct?username=%s', $username));
    }

    /**
     * Get nested user groups
     *
     * @param $username
     * @return mixed
     */
    public function getNestedGroups($username)
    {
        return $this->apiClient->callEndpoint(sprintf('user/group/nested?username=%s', $username));
    }
}