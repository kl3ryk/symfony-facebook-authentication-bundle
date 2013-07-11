<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\DependencyInjection;

use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;
use Laelaps\PHPUnit\TestAware\Fixture\SplObserver as SplObserverFixture;
use PHPUnit_Framework_TestCase;

class FacebookAuthenticationExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \BadMethodCallException
     */
    public function testThatFacebookApplicationConfigurationCannotBeFetchedIfNotSet()
    {
        $extension = new FacebookAuthenticationExtension;

        $this->assertFalse($extension->hasFacebookApplicationConfiguration());

        $extension->getFacebookApplicationConfiguration();
    }

    public function testThatFacebookApplicationConfigurationCanBeSet()
    {
        $config = [ uniqid() => uniqid() ];

        $extension = new FacebookAuthenticationExtension;

        $this->assertFalse($extension->hasFacebookApplicationConfiguration());

        $extension->setFacebookApplicationConfiguration($config);

        $this->assertTrue($extension->hasFacebookApplicationConfiguration());
        $this->assertSame($config, $extension->getFacebookApplicationConfiguration());
    }

    public function testThatObserverCanBeAttached()
    {
        $extension = new FacebookAuthenticationExtension;

        $observer = new SplObserverFixture($this);

        $extension->attach($observer);

        $observer->assertIsNotNotifiedBy($extension);
    }

    /**
     * @expectedException \OverflowException
     */
    public function testThatObserverCannotBeAttachedTwice()
    {
        $extension = new FacebookAuthenticationExtension;

        $observer = new SplObserverFixture($this);

        $extension->attach($observer);
        $extension->attach($observer);
    }

    public function testThatObserverIsInstantlyNotified()
    {
        $extension = new FacebookAuthenticationExtension;
        $extension->setFacebookApplicationConfiguration([ uniqid() => uniqid() ]);

        $observer = new SplObserverFixture($this);

        $extension->attach($observer);

        $observer->assertIsNotifiedBy($extension);
    }

    public function testThatObserverCanBeDetached()
    {
        $extension = new FacebookAuthenticationExtension;

        $observer = new SplObserverFixture($this);

        $extension->attach($observer);
        $extension->detach($observer);

        $extension->notify();

        $observer->assertIsNotNotifiedBy($extension);
    }

    /**
     * @expectedException \UnderflowException
     */
    public function testThatObserverCannotBeDetachedTwice()
    {
        $extension = new FacebookAuthenticationExtension;

        $observer = new SplObserverFixture($this);

        $extension->attach($observer);

        $extension->detach($observer);
        $extension->detach($observer);
    }
}
