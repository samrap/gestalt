<?php

namespace Gestalt\Util;

/**
 * An Observer is a typical implementation of an observer object using the
 * Observer Pattern.
 */
interface ObserverInterface
{
    /**
     * Update the observer.
     *
     * @param  \Gestalt\Util\Observable $observable
     * @return void
     */
    public function update(Observable $observable);
}
