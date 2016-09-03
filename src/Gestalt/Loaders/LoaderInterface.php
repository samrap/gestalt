<?php

namespace Gestalt\Loaders;

/**
 * A LoaderInterface defines a way for the Configuration class to instantiate
 * itself with any concrete class that implements this interface.
 *
 * @author Sam Rapaport
 */
interface LoaderInterface
{
    /**
     * Load the configuration items and return them as an array.
     *
     * @return array
     */
    public function load();
}
