<?php

namespace Bluetea\Api\Authentication;

interface AuthenticationInterface
{
    /**
     * @return string|null
     */
    public function getCredential();

    /**
     * @return string|null
     */
    public function getUsername();

    /**
     * @return string|null
     */
    public function getPassword();
} 