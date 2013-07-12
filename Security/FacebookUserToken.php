<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class FacebookUserToken extends AbstractToken
{
    /**
     * {@inheritDoc}
     */
    public function __construct($user, array $roles = null)
    {
        if (is_null($roles) && $user instanceof UserInterface) {
            $roles = $user->getRoles();
        }

        parent::__construct($roles ?: []);

        $this->setUser($user);
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        // no sensitive data in this token
        return '';
    }
}
