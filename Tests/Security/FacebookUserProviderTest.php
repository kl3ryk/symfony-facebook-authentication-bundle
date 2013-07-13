<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Security;

use Laelaps\Bundle\Facebook\FacebookAdapter\FacebookAdapterMock;
use Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserToken;
use Laelaps\Bundle\FacebookAuthentication\Tests\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FacebookUserProviderTest extends KernelTestCase
{
    /**
     * @return \Laelaps\Bundle\Facebook\FacebookAdapter\FacebookAdapterMock
     */
    private function getFacebookAdapterMock()
    {
        $container = $this->getContainer();
        $facebookAdapter = new FacebookAdapterMock([
            'appId' => uniqid(),
            'secret' => uniqid(),
        ], $container->get('session'));

        return $facebookAdapter;
    }

    /**
     * @param bool $shouldCreateUserByDefault
     * @param array $predefinedUsers
     * @param array $usersExpectedToBeCreated
     * @return \Laelaps\Bundle\FacebookAuthentication\Security\FacebookFirewallListener
     */
    private function getFacebookUserProvider($shouldCreateUserByDefault = true, array $predefinedUsers = [], array $usersExpectedToBeCreated = [])
    {
        $stub = $this->getMockForAbstractClass('Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserProvider');

        $stub->expects($this->any())
            ->method('createUserByFacebookData')
            ->will($this->returnCallback(function ($username, array $facebookData) use (&$predefinedUsers, &$usersExpectedToBeCreated) {
                $this->assertArrayHasKey($username, $usersExpectedToBeCreated, 'failed asserting that user is expected to be created');
                $this->assertSame($usersExpectedToBeCreated[$username], $facebookData, 'failed asserting that expected mock facebook data is correct');

                $predefinedUsers[$username] = $this->getUserMock($username);
            }))
        ;

        $stub->expects($this->any())
            ->method('doLoadUserByUsername')
            ->will($this->returnCallback(function ($username) use (&$predefinedUsers) {
                return isset($predefinedUsers[$username]) ? $predefinedUsers[$username] : null;
            }))
        ;

        $stub->expects($this->any())
            ->method('shouldCreateUserByDefault')
            ->will($this->returnValue($shouldCreateUserByDefault))
        ;

        return $stub;
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

    public function testThatExistingUserCanBeLoaded()
    {
        $username = uniqid();
        $user = $this->getUserMock($username);

        $facebookUserProvider = $this->getFacebookUserProvider($shouldCreateUserByDefault = false, [
            $username => $user,
        ]);

        $this->assertSame($user, $facebookUserProvider->loadUserByUsername($username));
    }

    public function testThatNotExistingUserIsCreatedByDefault()
    {
        $username = uniqid();
        $mockedFacebookData = [ 'id' => $username ];

        $facebookUserProvider = $this->getFacebookUserProvider($shouldCreateUserByDefault = true, $predefinedUsers = [], $usersExpectedToBeCreated = [
            $username => $mockedFacebookData,
        ]);

        $facebookAdapter = $this->getFacebookAdapterMock();
        $facebookAdapter->setMockedGraphApiCall('/' . $username, $mockedFacebookData);
        $facebookUserProvider->setFacebookAdapter($facebookAdapter);

        $user = $facebookUserProvider->loadUserByUsername($username);

        $this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface', $user);
        $this->assertSame($username, $user->getUsername());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testThatNotExistingUserIsNotCreatedByDefault()
    {
        $facebookUserProvider = $this->getFacebookUserProvider($shouldCreateUserByDefault = false);
        $facebookUserProvider->loadUserByUsername(uniqid());
    }

    public function testThatNotExistingUserIsCreatedWhenForced()
    {
        $username = uniqid();
        $mockedFacebookData = [ 'id' => $username ];

        $facebookUserProvider = $this->getFacebookUserProvider($shouldCreateUserByDefault = false, $predefinedUsers = [], $usersExpectedToBeCreated = [
            $username => $mockedFacebookData,
        ]);
        $facebookUserProvider->setShouldCreateUser($username, true);

        $facebookAdapter = $this->getFacebookAdapterMock();
        $facebookAdapter->setMockedGraphApiCall('/' . $username, $mockedFacebookData);
        $facebookUserProvider->setFacebookAdapter($facebookAdapter);

        $user = $facebookUserProvider->loadUserByUsername($username);

        $this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface', $user);
        $this->assertSame($username, $user->getUsername());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testThatNotExistingUserIsNotCreatedWhenForced()
    {
        $username = uniqid();
        $mockedFacebookData = [ 'id' => $username ];

        $facebookUserProvider = $this->getFacebookUserProvider($shouldCreateUserByDefault = true, $predefinedUsers = [], $usersExpectedToBeCreated = [
            $username => $mockedFacebookData,
        ]);
        $facebookUserProvider->setShouldCreateUser($username, false);
        $facebookUserProvider->loadUserByUsername($username);
    }
}
