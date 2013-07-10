<?php

namespace Laelaps\Bundle\FacebookAuthentication\Security;

use BadMethodCallException;
use Laelaps\Bundle\Facebook\FacebookAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class FacebookFirewallListener implements ListenerInterface
{
    /**
     * @var string
     */
    const SESSION_USER_FACEBOOK_ID = 'user_facebook_id';

    /**
     * @var \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var \Laelaps\Bundle\Facebook\FacebookAdapter
     */
    private $facebookAdapter;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param string $user
     * @param \Laelaps\Bundle\Facebook\FacebookAdapter $facebookAdapter
     * @return \Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserToken
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticateUser($user, FacebookAdapter $facebookAdapter)
    {
        $token = new FacebookUserToken($user);

        try {
            $authToken = $this->authenticationManager->authenticate($token, $facebookAdapter);
        } catch (AuthenticationException $failed) {
            $this->securityContext->setToken($token);

            throw $failed;
        }

        $this->securityContext->setToken($authToken);

        return $authToken;
    }

    /**
     * @return \Laelaps\Bundle\Facebook\FacebookAdapter
     * @throws \BadMethodCallException
     */
    public function getFacebookAdapter()
    {
        if (!($this->facebookAdapter instanceof FacebookAdapter)) {
            throw new BadMethodCallException('FacebookAdapter is not set.');
        }

        return $this->facebookAdapter;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @return void
     */
    public function handle(GetResponseEvent $event)
    {
        $userFacebookId = $this->getFacebookAdapter()->getUser();

        if ($userFacebookId) {
            $this->authenticateUser($userFacebookId);
        }
    }

    /**
     * @param \Laelaps\Bundle\Facebook\FacebookAdapter $facebook
     * @return void
     */
    public function setFacebookAdapter(FacebookAdapter $facebook)
    {
        $this->facebookAdapter = $facebook;
    }
}
