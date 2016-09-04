<?php

use Gestalt\Loaders\DirectoryLoader;

class DirectoryLoaderTest extends PHPUnit_Framework_TestCase
{
    public function test_load_method_returns_configuration_array()
    {
        $loader = new DirectoryLoader(__DIR__.'/config');
        $loaded = $loader->load();

        $this->assertArrayHasKey('foo', $loaded);
        $this->assertArrayHasKey('bar', $loaded);
    }
}
