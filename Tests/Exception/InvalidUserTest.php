<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Exception;

use Laelaps\Bundle\FacebookAuthentication\Exception\InvalidUser as InvalidUserException;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class InvalidUserTest extends PHPUnit_Framework_TestCase
{
    public function testThatExceptionCanBeCreated()
    {
        $exception = new InvalidUserException(uniqid());

        $message = $exception->getMessage();

        $this->assertTrue(is_string($message));
        $this->assertNotEmpty($message);
    }

    public function testThatExceptionCanBeCreatedWithUserInterface()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $exception = new InvalidUserException($user);

        $message = $exception->getMessage();

        $this->assertTrue(is_string($message));
        $this->assertNotEmpty($message);
    }
}
