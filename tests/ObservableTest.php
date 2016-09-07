<?php

use Gestalt\Util\Observable;

class ObservableTest extends PHPUnit_Framework_TestCase
{
    /**
     * A test object using the \Gestalt\Util\Observable trait.
     */
    protected $observable;

    /**
     * Set up the test environment.
     */
    public function setUp()
    {
        $this->observable = new Observable;
    }

    /**
     * Get a mocked Observer implementation.
     */
    protected function getObserver($maxUpdates = 0)
    {
        $observer = Mockery::mock('Gestalt\Util\ObserverInterface');

        if ($maxUpdates > 0) {
            $observer->shouldReceive('update')->with($this->observable)->times($maxUpdates);
        } else {
            $observer->shouldReceive('update')->with($this->observable);
        }

        return $observer;
    }

    public function test_observable_attaches_observer()
    {
        $this->observable->attach($this->getObserver());
        $this->observable->notify();
    }

    public function test_observable_detaches_observer()
    {
        // Add an observer that expects to only be updated once. This will throw
        // an error if it is updated multiple times.
        $observer = $this->getObserver(1);

        $this->observable->attach($observer);
        $this->observable->notify();
        $this->observable->detach($observer);
        $this->observable->notify();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
