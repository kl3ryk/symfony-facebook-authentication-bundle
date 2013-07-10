<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use Laelaps\Bundle\Facebook\FacebookAdapter;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
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
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if (!$user) {
            return $this->notifyAuthenticationFailure($token);
        }

        $authenticatedToken = new FacebookUserToken($user, $user->getRoles());

        $this->notifyAuthenticationSuccess($authenticatedToken);

        return $authenticatedToken;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return bool
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function notifyAuthenticationFailure(TokenInterface $token)
    {
        throw new AuthenticationException('Facebook authentication failed');
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return bool
     */
    public function notifyAuthenticationSuccess(TokenInterface $token)
    {
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
