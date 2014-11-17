<?php

namespace Bluetea\Jira\Endpoint;

use Bluetea\Api\Request\HttpMethod;

class UserEndpoint extends BaseEndpoint implements EndpointInterface
{
    const ENDPOINT = 'user';

    /**
     * Returns a user. This resource cannot be accessed anonymously.
     *
     * @param $username
     * @return mixed
     */
    public function find($username)
    {
        $parameters['username'] = $username;
        return $this->apiClient->callEndpoint(self::ENDPOINT, $parameters);
    }

    /**
     * Modify user. The "value" fields present will override the existing value. Fields skipped in request will not be changed.
     *
     * @param $username
     * @param array $parameters
     * @return mixed
     */
    public function update($username, $parameters = array())
    {
        $parameters['username'] = $username;
        $this->apiClient->setHttpMethod(HttpMethod::REQUEST_PUT);
        return $this->apiClient->callEndpoint(self::ENDPOINT, $parameters);
    }

    /**
     * Create user. By default created user will not be notified with email. If password field is not set then password
     * will be randomly generated.
     *
     * @param $username
     * @param array $parameters
     * @return mixed
     */
    public function add($username, $parameters = array())
    {
        $parameters['username'] = $username;
        $this->apiClient->setHttpMethod(HttpMethod::REQUEST_POST);
        return $this->apiClient->callEndpoint(self::ENDPOINT, $parameters);
    }

    /**
     * Removes user.
     *
     * @param $username
     * @return mixed
     */
    public function delete($username)
    {
        $parameters['username'] = $username;
        $this->apiClient->setHttpMethod(HttpMethod::REQUEST_DELETE);
        return $this->apiClient->callEndpoint(self::ENDPOINT, $parameters);
    }

    /**
     * Returns all avatars which are visible for the currently logged in user.
     *
     * @param $username
     * @return mixed
     */
    public function findAvatars($username)
    {
        $parameters['username'] = $username;
        return $this->apiClient->callEndpoint(sprintf('%s/avatars', self::ENDPOINT), $parameters);
    }

    /**
     * Modify user password.
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public function updatePassword($username, $password)
    {
        $parameters['username'] = $username;
        $parameters['password'] = $password;
        $this->apiClient->setHttpMethod(HttpMethod::REQUEST_PUT);
        return $this->apiClient->callEndpoint(sprintf('%s/avatars', self::ENDPOINT), $parameters);
    }

    /**
     * Returns a list of users matching query with highlighting. This resource cannot be accessed anonymously.
     *
     * @param null|string $query
     * @param null|int $maxResults
     * @param bool $showAvatar
     * @param null|string $exclude
     * @return mixed
     */
    public function picker($query, $maxResults = null, $showAvatar = null, $exclude = null)
    {
        $parameters = array(
            'query' => $query,
            'maxResults' => $maxResults,
            'showAvatar' => $showAvatar,
            'exclude' => $exclude
        );
        return $this->apiClient->callEndpoint(sprintf('%s/picker', self::ENDPOINT), $parameters);
    }

    /**
     * Returns a list of users that match the search string. This resource cannot be accessed anonymously.
     *
     * @param $username
     * @param int $startAt
     * @param int $maxResults
     * @param bool $includeActive
     * @param bool $includeInactive
     * @return mixed
     */
    public function search($username, $startAt = null, $maxResults = null, $includeActive = null, $includeInactive = null)
    {
        $parameters = array(
            'username' => $username,
            'startAt' => $startAt,
            'maxResults' => $maxResults,
            'includeActive' => $includeActive,
            'includeInactive' => $includeInactive
        );
        return $this->apiClient->callEndpoint(sprintf('%s/search', self::ENDPOINT), $parameters);
    }
}