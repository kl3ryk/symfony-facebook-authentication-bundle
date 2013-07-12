<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Fixture\Security;

use Laelaps\PHPUnit\TestAware\PHPUnitAwareInterface;
use Laelaps\PHPUnit\TestAware\PHPUnitAwareTrait;
use OutOfRangeException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TestableUserProvider implements PHPUnitAwareInterface, UserProviderInterface
{
    use PHPUnitAwareTrait;

    /**
     * @var array
     */
    private $users = [];

    /**
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\ExceptionUsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        if ($this->hasPredefinedUser($username)) {
            return $this->getPredefinedUser($username);
        }

        return $this->getPHPUnit()
            ->getMock('Symfony\Component\Security\Core\User\UserInterface')
        ;
    }

    /**
     * @param string $username
     * @return mixed
     * @throws \OutOfRangeException
     */
    public function getPredefinedUser($username)
    {
        if (!$this->hasPredefinedUser($username)) {
            throw new OutOfRangeException(sprintf('User "%s" is not predefined.', $username));
        }

        return $this->users[$username];
    }

    /**
     * @param string $username
     * @return bool
     */
    public function hasPredefinedUser($username)
    {
        return array_key_exists($username, $this->users);
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $username
     * @param mixed $user
     * @return void
     */
    public function setPredefinedUser($username, $user)
    {
        $this->users[$username] = $user;
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
