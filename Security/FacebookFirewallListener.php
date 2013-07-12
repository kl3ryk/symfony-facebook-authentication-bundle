<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use BadMethodCallException;
use Laelaps\Bundle\Facebook\FacebookAdapterAwareInterface;
use Laelaps\Bundle\Facebook\FacebookAdapterAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class FacebookFirewallListener extends AbstractAuthenticationListener implements FacebookAdapterAwareInterface
{
    use FacebookAdapterAwareTrait;

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $userFacebookId = $this->getFacebookAdapter()->getUser();

        if ($userFacebookId) {
            $token = new FacebookUserToken($userFacebookId);

            return $this->getAuthenticationManager()->authenticate($token);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        // those are fixed Facebook internal authentication parameters
        return $request->query->has('code') && $request->query->has('state');
    }
}
