<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Security;

use Laelaps\Bundle\Facebook\FacebookAdapter\FacebookAdapterMock;
use Laelaps\Bundle\FacebookAuthentication\Security\FacebookUserToken;
use Laelaps\Bundle\FacebookAuthentication\Tests\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\Role;

class FacebookUserTokenTest extends KernelTestCase
{
    public function testThatFacebookUserTokenIsCreatedWithUsername()
    {
        $username = uniqid();
        $token = new FacebookUserToken($username);

        $this->assertSame($username, $token->getUsername());
        $this->assertEmpty($token->getRoles());
    }

    public function testThatFacebookUserTokenIsCreatedWithUsernameAndRoles()
    {
        $username = uniqid();
        $roles = [uniqid(), uniqid()];
        $token = new FacebookUserToken($username, $roles);

        $this->assertSame($username, $token->getUsername());
        $this->assertSame($roles, array_map(function (Role $role) {
            return $role->getRole();
        }, $token->getRoles()));
    }

    public function testThatFacebookUserTokenIsCreatedWithUser()
    {
        $username = uniqid();

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username))
        ;

        $token = new FacebookUserToken($user);

        $this->assertSame($user, $token->getUser());
        $this->assertSame($username, $token->getUsername());
        $this->assertEmpty($token->getRoles());
    }

    public function testThatFacebookUserTokenIsCreatedWithUserWithRoles()
    {
        $username = uniqid();
        $roles = [uniqid(), uniqid()];

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username))
        ;
        $user->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles))
        ;

        $token = new FacebookUserToken($user);

        $this->assertSame($user, $token->getUser());
        $this->assertSame($username, $token->getUsername());
        $this->assertSame($roles, array_map(function (Role $role) {
            return $role->getRole();
        }, $token->getRoles()));
    }

    public function testThatFacebookUserTokenIsCreatedWithUserAndRoles()
    {
        $username = uniqid();
        $roles = [uniqid(), uniqid()];

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username))
        ;

        $token = new FacebookUserToken($user, $roles);

        $this->assertSame($user, $token->getUser());
        $this->assertSame($username, $token->getUsername());
        $this->assertSame($roles, array_map(function (Role $role) {
            return $role->getRole();
        }, $token->getRoles()));
    }

    public function testThatFacebookUserTokenIsCreatedWithUserAndRolesAndTokenRolesPrevail()
    {
        $username = uniqid();
        $userRoles = [uniqid(), uniqid()];
        $tokenRoles = [uniqid(), uniqid()];

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username))
        ;
        $user->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($userRoles))
        ;

        $token = new FacebookUserToken($user, $tokenRoles);

        $this->assertSame($user, $token->getUser());
        $this->assertSame($username, $token->getUsername());
        $this->assertSame($tokenRoles, array_map(function (Role $role) {
            return $role->getRole();
        }, $token->getRoles()));
    }
}
