<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Security;

use Laelaps\Bundle\Facebook\FacebookAdapter\FacebookAdapterMock;
use Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserToken;
use Laelaps\Bundle\FacebookAuthentication\Tests\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FacebookFirewallListenerTest extends KernelTestCase
{
    /**
     * @return \Laelaps\Bundle\FacebookAuthentication\Security\FacebookFirewallListener
     */
    private function getFacebookFirewallListener()
    {
        $serviceId = 'laelaps.security.facebook.firewall_listener.main.facebook';

        return $this->getContainer()->get($serviceId);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Event\GetResponseEvent
     */
    private function getGetResponseEvent()
    {
        $kernel = $this->getKernel();
        $session = $this->getContainer()->get('session');

        $request = new Request;
        $request->setSession($session);

        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        return $event;
    }

    /**
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    private function getUserMock($username)
    {
        $stub = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')
            ->getMock()
        ;

        $stub->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username))
        ;

        return $stub;
    }

    public function testThatAuthenticationIsNotFiredWithMissingFacebookData()
    {
        $getResponseEvent = $this->getGetResponseEvent();
        $firewallListener = $this->getFacebookFirewallListener();
        $response = $firewallListener->handle($getResponseEvent);
        $this->assertNull($response);
    }

    public function testThatAuthenticationIsFiredWithFacebookData()
    {
        $container = $this->getContainer();

        $username = uniqid();
        $getResponseEvent = $this->getGetResponseEvent();

        $request = $getResponseEvent->getRequest();
        // these two are required to mock facebook authentication response
        $request->query->set('code', uniqid());
        $request->query->set('state', uniqid());

        // this one is required to mock user with this test username
        $container->get('laelaps.security.facebook.authentication_provider.main.facebook')
            ->getUserProvider()
            ->setPredefinedUser($username, $this->getUserMock($username))
        ;

        // this block of code is required to mock Facebook SDK user
        $firewallListener = $this->getFacebookFirewallListener();
        $facebook = $firewallListener->getFacebookAdapter();
        $facebook = FacebookAdapterMock::fromFacebookAdapter($facebook);
        $facebook->setUser($username);
        $firewallListener->setFacebookAdapter($facebook);

        // ...and now some assertions

        $securityContext = $container->get('security.context');

        $token = $securityContext->getToken();
        $this->assertNull($token, 'failed asserting that security context has no token before authentication');

        $firewallListener->handle($getResponseEvent);

        $token = $securityContext->getToken();

        $this->assertInstanceof('Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserToken', $token, 'failed asserting that security context token is a Facebook user token');
        $this->assertSame($username, $token->getUsername(), 'failed asserting that security context token username is correct username');
        $this->assertTrue($token->isAuthenticated(), 'failed asserting that token is authenticated');
    }
}
