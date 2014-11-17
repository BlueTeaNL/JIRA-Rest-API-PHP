<?php

namespace Bluetea\Jira\Endpoint;

class Project extends BaseEndpoint implements EndpointInterface
{
    const ENDPOINT = 'project';

    /**
     * Returns all project which are visible for the currently logged in user. If no user logged in it returns the list
     * of projects that are visible when using anonymous access
     *
     * @return mixed
     */
    public function all()
    {
        return $this->apiClient->callEndpoint(self::ENDPOINT);
    }
} 