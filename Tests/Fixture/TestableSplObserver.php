<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Fixture;

use PHPUnit_Framework_TestCase;
use SplObjectStorage;
use SplObserver;
use SplSubject;

class TestableSplObserver implements SplObserver
{
    /**
     * @var \PHPUnit_Framework_TestCase
     */
    private $phpunit;

    /**
     * @var \SplObjectStorage
     */
    private $notifications;

    public function __construct(PHPUnit_Framework_TestCase $phpunit)
    {
        $this->notifications = new SplObjectStorage;
        $this->phpunit = $phpunit;
    }

    /**
     * @param \SplSubject $subject
     * @return bool
     */
    public function isNotifiedBy(SplSubject $subject)
    {
        return $this->notifications->contains($subject);
    }

    /**
     * @param \SplSubject $subject
     * @return void
     */
    public function update(SplSubject $subject)
    {
        $this->notifications->attach($subject);
    }
}
