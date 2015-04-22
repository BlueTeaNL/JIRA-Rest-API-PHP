<?php

namespace Bluetea\Jira\Endpoint;

use Bluetea\Api\Endpoint\BaseEndpoint;
use Bluetea\Api\Endpoint\EndpointInterface;
use Bluetea\Api\Exception\EndpointParameterException;
use Bluetea\Api\Request\HttpMethod;

class ProjectEndpoint extends BaseEndpoint implements EndpointInterface
{
    const ENDPOINT = 'project';

    /**
     * Returns all project which are visible for the currently logged in user. If no user logged in it returns the list
     * of projects that are visible when using anonymous access
     *
     * @return mixed
     */
    public function findAll()
    {
        return $this->apiClient->callEndpoint(self::ENDPOINT);
    }

    /**
     * Contains a full representation of a project
     *
     * @param int|string $projectId
     * @return mixed
     */
    public function find($projectId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s', self::ENDPOINT, $projectId));
    }

    /**
     * Converts temporary avatar into a real avatar
     *
     * @param int|string $projectId
     * @param int $cropperWidth
     * @param int $cropperOffsetX
     * @param int $cropperOffsetY
     * @param bool $needsCropping
     * @return mixed
     */
    public function convertAvatar($projectId, $cropperWidth, $cropperOffsetX, $cropperOffsetY, $needsCropping)
    {
        return $this->apiClient->callEndpoint(
            sprintf('%s/%s/avatar', self::ENDPOINT, $projectId),
            array(
                "cropperWidth" => $cropperWidth,
                "cropperOffsetX" => $cropperOffsetX,
                "cropperOffsetY" => $cropperOffsetY,
                "needsCropping" => $needsCropping
            ),
            HttpMethod::REQUEST_POST
        );
    }

    /**
     * Delete avatar
     *
     * @param $projectId
     * @param $avatarId
     * @return mixed
     */
    public function deleteAvatar($projectId, $avatarId)
    {
        return $this->apiClient->callEndpoint(
            sprintf('%s/%s/avatar/%s', self::ENDPOINT, $projectId, $avatarId),
            array(),
            null,
            HttpMethod::REQUEST_DELETE
        );
    }

    /**
     * Returns all avatars which are visible for the currently logged in user. The avatars are grouped into system and custom.
     *
     * @param $projectId
     * @return mixed
     */
    public function findAvatars($projectId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/avatars', self::ENDPOINT, $projectId));
    }

    /**
     * Contains a full representation of a the specified project's components.
     *
     * @param $projectId
     * @return mixed
     */
    public function findComponents($projectId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/components', self::ENDPOINT, $projectId));
    }

    /**
     * Get all issue types with valid status values for a project
     *
     * @param $projectId
     * @return mixed
     */
    public function findStatuses($projectId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/statuses', self::ENDPOINT, $projectId));
    }

    /**
     * Contains a full representation of a the specified project's versions.
     *
     * @param $projectId
     * @return mixed
     */
    public function findVersions($projectId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/versions', self::ENDPOINT, $projectId));
    }

    /**
     * Returns the keys of all properties for the project identified by the key or by the id.
     * 
     * @param $projectId
     * @return mixed
     */
    public function findProperties($projectId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/properties', self::ENDPOINT, $projectId));
    }

    /**
     * Returns the value of the property with a given key from the project identified by the key or by the id. The user
     * who retrieves the property is required to have permissions to read the project.
     *
     * @param $projectId
     * @param $propertyKey
     * @return mixed
     */
    public function findProperty($projectId, $propertyKey)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/properties/%s', self::ENDPOINT, $projectId, $propertyKey));
    }

    /**
     * Contains a list of roles in this project with links to full details.
     *
     * @param $projectId
     * @return mixed
     */
    public function findRoles($projectId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/role', self::ENDPOINT, $projectId));
    }

    /**
     * Details on a given project role.
     *
     * @param $projectId
     * @param $roleId
     * @return mixed
     */
    public function findRole($projectId, $roleId)
    {
        return $this->apiClient->callEndpoint(sprintf('%s/%s/role/%s', self::ENDPOINT, $projectId, $roleId));
    }

    /**
     * Updates a project role to contain the sent actors.
     *
     * @param $projectId
     * @param $roleId
     * @param null|string $user
     * @param null|string $group
     * @throws \Bluetea\Api\Exception\EndpointParameterException
     * @return mixed
     */
    public function updateRole($projectId, $roleId, $user = null, $group = null)
    {
        $parameters = [];
        if ((is_null($user) && is_null($group)) || (!is_null($user) && !is_null($group))) {
            throw new EndpointParameterException('User or group should be given');
        } elseif (!is_null($user)) {
            $parameters['user'] = $user;
        } else {
            $parameters['group'] = $group;
        }

        return $this->apiClient->callEndpoint(
            sprintf('%s/%s/role/%s', self::ENDPOINT, $projectId, $roleId),
            $parameters,
            null,
            HttpMethod::REQUEST_PUT
        );
    }

    /**
     * Add an actor to a project role.
     *
     * @param $projectId
     * @param $roleId
     * @param null|string $user
     * @param null|string $group
     * @throws \Bluetea\Api\Exception\EndpointParameterException
     * @return mixed
     */
    public function addRole($projectId, $roleId, $user = null, $group = null)
    {
        $parameters = [];
        if ((is_null($user) && is_null($group)) || (!is_null($user) && !is_null($group))) {
            throw new EndpointParameterException('User or group should be given');
        } elseif (!is_null($user)) {
            $parameters['user'] = $user;
        } else {
            $parameters['group'] = $group;
        }

        return $this->apiClient->callEndpoint(
            sprintf('%s/%s/role/%s', self::ENDPOINT, $projectId, $roleId),
            $parameters,
            null,
            HttpMethod::REQUEST_POST
        );
    }

    /**
     * Remove actors from a project role.
     *
     * @param $projectId
     * @param $roleId
     * @return mixed
     */
    public function deleteRole($projectId, $roleId)
    {
        return $this->apiClient->callEndpoint(
            sprintf('%s/%s/role/%s', self::ENDPOINT, $projectId, $roleId),
            array(),
            null,
            HttpMethod::REQUEST_DELETE
        );
    }
} 