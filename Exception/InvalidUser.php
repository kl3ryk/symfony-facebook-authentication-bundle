<?php

namespace Laelaps\Bundle\FacebookAuthentication\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class InvalidUser extends AuthenticationException
{
    /**
     * @param mixed $user
     */
    public function __construct($user)
    {
    }
}
