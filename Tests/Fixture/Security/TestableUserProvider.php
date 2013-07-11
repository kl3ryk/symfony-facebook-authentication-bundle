<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Fixture\Security;

use Laelaps\PHPUnit\TestAware\PHPUnitAwareInterface;
use Laelaps\PHPUnit\TestAware\PHPUnitAwareTrait;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TestableUserProvider implements PHPUnitAwareInterface, UserProviderInterface
{
    use PHPUnitAwareTrait;

    /**
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\ExceptionUsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        var_dump(__METHOD__);
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        var_dump(__METHOD__);
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return true;
    }
}
