JIRA Rest API for PHP
=====================

Atlassian JIRA contains a REST API. With this PHP Library you can use this REST API on an object oriented way.
If you want to use this library in a Symfony2 project, a Symfony2 bundle will be available soon!

# Usage

Usage is very easy! First set up the connection to your Atlassian JIRA server and then call some endpoints.

```
<?php

namespace Acme\DemoBundle\Controller;

use Bluetea\Api\Authentication\BasicAuthentication;
use Bluetea\Api\Client\CurlClient;
use Bluetea\Api\Client\GuzzleClient;
use Bluetea\Jira\Endpoint\ProjectEndpoint;


class TestController extends Controller
{
    public function testAction()
    {
        // With Curl
        $apiClient = new CurlClient(
            'https://atlassian.yourdomain.com/rest/api/2',
            new BasicAuthentication('username', 'password')
        );

        // Or Guzzle if you like
        $apiClient = new GuzzleClient(
            'https://atlassian.yourdomain.com/rest/api/2',
            new BasicAuthentication('username', 'password')
        );

        $projectEndpoint = new ProjectEndpoint($apiClient);
        // Get all projects
        return new JsonResponse($projectEndpoint->findAll());
    }
}
```

Check the `Jira/Endpoint` namespace for more endpoints and actions. Please help us to adding all the JIRA endpoints by
 submitting a PR!

# Documentation

<a href="https://docs.atlassian.com/jira/REST/6.3.10/">JIRA 6.3.10 REST API Documentation</a>
<a href="https://developer.atlassian.com/display/JIRADEV/JIRA+REST+APIs">JIRA REST API Developers Documentation</a>

# Debug

Install the JIRA REST API browser to test your calls. You find it at https://atlassian.yourdomain.com/plugins/servlet/restbrowser.
Your getting a 404? Install the plugin first via the plugin manager.