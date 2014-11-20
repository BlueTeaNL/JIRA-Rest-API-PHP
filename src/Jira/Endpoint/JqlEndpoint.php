<?php

namespace Bluetea\Jira\Endpoint;

use Bluetea\Api\Endpoint\BaseEndpoint;
use Bluetea\Api\Endpoint\EndpointInterface;

class JqlEndpoint extends BaseEndpoint implements EndpointInterface
{
    /**
     * Searches for issues using JQL
     *
     * @param $jql
     * @param null $startAt
     * @param null $maxResults
     * @param null $validateQuery
     * @param null $fields
     * @param null $expand
     *
     * @return mixed
     */
    public function search($jql, $startAt = null, $maxResults = null, $validateQuery = null, $fields = null, $expand = null)
    {
        $parameters = array(
            'jql' => $jql,
            'startAt' => $startAt,
            'maxResults' => $maxResults,
            'validateQuery' => $validateQuery,
            'fields' => $fields,
            'expand' => $expand
        );
        return $this->apiClient->callEndpoint('search', $parameters);
    }
}