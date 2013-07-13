<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use Laelaps\Bundle\Facebook\FacebookAdapterAwareInterface;
use Laelaps\Bundle\Facebook\FacebookAdapterAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

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

            return $this->authenticationManager->authenticate($token);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        // these are fixed Facebook internal authentication parameters
        return $request->get('code') && $request->get('state');
    }

    /**
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface $authenticationManager
     * @param \SessionAuthenticationStrategyInterface $sessionStrategy
     * @param \Symfony\Component\Security\Http\HttpUtils $httpUtils
     * @param string $providerKey
     * @param \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface $successHandler
     * @param \Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface $failureHandler
     * @param array $options
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @throws \InvalidArgumentException
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        // this needs to be disabled because Facebook authentication can be started almost anywhere, anytime
        $options['require_previous_session'] = false;

        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, $options, $logger, $dispatcher);
    }
}
