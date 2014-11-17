<?php

namespace Bluetea\Jira\Endpoint;

use Bluetea\Api\Client\ClientInterface;

abstract class BaseEndpoint implements EndpointInterface
{
    /**
     * @var ClientInterface
     */
    protected $apiClient;

    public function __construct(ClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }
} 