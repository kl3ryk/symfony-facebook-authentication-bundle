<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Security;

use Laelaps\Bundle\FacebookAuthentication\Security\FacebookAuthenticationProvider;
use Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserToken;
use Laelaps\Bundle\FacebookAuthentication\Tests\KernelTestCase;
use stdClass;

class FacebookAuthenticationProviderTest extends KernelTestCase
{
    /**
     * @return \Laelaps\Bundle\FacebookAuthentication\Security\FacebookAuthenticationProvider
     */
    private function getFacebookAuthenticationProvider()
    {
        $container = $this->getContainer();

        $userProvider = $container->get('security.user_provider.testable');
        $userProvider->setPHPUnit($this);

        $facebookAuthenticationProvider = new FacebookAuthenticationProvider;
        $facebookAuthenticationProvider->setUserProvider($userProvider);

        return $facebookAuthenticationProvider;
    }

    public function testThatCorrectFacebookUserTokenIsAuthenticated()
    {
        $authenticationProvider = $this->getFacebookAuthenticationProvider();
        $facebookUserToken = new FacebookUserToken($fakeFacebookId = uniqid());

        $authenticatedToken = $authenticationProvider->authenticate($facebookUserToken);

        $this->assertInstanceOf('Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserToken', $authenticatedToken);
        $this->assertNotSame($authenticatedToken, $facebookUserToken);
        $this->assertTrue($authenticatedToken->isAuthenticated());
        $this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface', $authenticatedToken->getUser());
    }

    /**
     * @expectedException \Laelaps\Bundle\FacebookAuthentication\Exception\InvalidUser
     */
    public function testThatInvalidUserIsDetected()
    {
        $authenticationProvider = $this->getFacebookAuthenticationProvider();
        $fakeFacebookId = uniqid();

        $authenticationProvider->getUserProvider()
            ->setPredefinedUser($fakeFacebookId, $invalidUser = new stdClass)
        ;

        $facebookUserToken = new FacebookUserToken($fakeFacebookId);
        $authenticatedToken = $authenticationProvider->authenticate($facebookUserToken);
    }
}
