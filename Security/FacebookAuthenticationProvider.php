<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use BadMethodCallException;
use Laelaps\Bundle\FacebookAuthentication\Exception\InvalidUser as InvalidUserException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FacebookAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $userProvider;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return bool
     * @throws \Laelaps\Bundle\FacebookAuthentication\Exception\InvalidUser
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->getUserProvider()
            ->loadUserByUsername($token->getUsername())
        ;

        if (!($user instanceof UserInterface)) {
            throw new InvalidUserException($user);
        }

        $authenticatedToken = new FacebookUserToken($user);
        $authenticatedToken->setAuthenticated(true);

        return $authenticatedToken;
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @throws \BadMethodCallException
     */
    public function getUserProvider()
    {
        if (!($this->userProvider instanceof UserProviderInterface)) {
            throw new BadMethodCallException('UserProvider is not set.');
        }

        return $this->userProvider;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @return void
     */
    public function setUserProvider(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return bool
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof FacebookUserToken;
    }
}
