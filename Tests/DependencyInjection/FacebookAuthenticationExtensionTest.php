<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\DependencyInjection;

use Laelaps\Bundle\FacebookAuthentication\DependencyInjection\FacebookAuthenticationExtension;
use Laelaps\Bundle\FacebookAuthentication\Tests\Fixture\TestableSplObserver;
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

        $observer = new TestableSplObserver($this);

        $extension->attach($observer);

        $this->assertFalse($observer->isNotifiedBy($extension));
    }

    /**
     * @expectedException \OverflowException
     */
    public function testThatObserverCannotBeAttachedTwice()
    {
        $extension = new FacebookAuthenticationExtension;

        $observer = new TestableSplObserver($this);

        $extension->attach($observer);
        $extension->attach($observer);
    }

    public function testThatObserverIsInstantlyNotified()
    {
        $extension = new FacebookAuthenticationExtension;
        $extension->setFacebookApplicationConfiguration([ uniqid() => uniqid() ]);

        $observer = new TestableSplObserver($this);

        $extension->attach($observer);

        $this->assertTrue($observer->isNotifiedBy($extension));
    }

    public function testThatObserverCanBeDetached()
    {
        $extension = new FacebookAuthenticationExtension;

        $observer = new TestableSplObserver($this);

        $extension->attach($observer);
        $extension->detach($observer);

        $extension->notify();

        $this->assertFalse($observer->isNotifiedBy($extension));
    }

    /**
     * @expectedException \UnderflowException
     */
    public function testThatObserverCannotBeDetachedTwice()
    {
        $extension = new FacebookAuthenticationExtension;

        $observer = new TestableSplObserver($this);

        $extension->attach($observer);

        $extension->detach($observer);
        $extension->detach($observer);
    }
}
