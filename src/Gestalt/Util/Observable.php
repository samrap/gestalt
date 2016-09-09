<?php

namespace Gestalt\Util;

class Observable
{
    /**
     * The attached ovservers.
     *
     * @var array
     */
    private $observers = [];

    /**
     * Attach a new observer to the list of observers.
     *
     * @param  \Gestalt\Util\ObserverInterface $observer
     * @return void
     */
    public function attach(ObserverInterface $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Notify the observers.
     *
     * @return void
     */
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Detach an observer from the list of observers.
     *
     * @param  \Gestalt\Util\ObserverInterface $observer
     * @return void
     */
    public function detach(ObserverInterface $observer)
    {
        if (($key = array_search($observer, $this->observers, true)) !== false) {
            unset($this->observers[$key]);
        }
    }
}
