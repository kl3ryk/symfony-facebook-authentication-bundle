<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use BadMethodCallException;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class FacebookUserToken extends AbstractToken
{
    /**
     * {@inheritDoc}
     */
    public function __construct($user, array $roles = [])
    {
        parent::__construct($roles);

        $this->setUser($user);
    }
}
