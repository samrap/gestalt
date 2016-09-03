<?php

use Gestalt\Loaders\FileLoader;

class FileLoaderTest extends PHPUnit_Framework_TestCase
{
    public function test_load_method_returns_configuration_array()
    {
        $loader = new FileLoader(__DIR__.'/config');
        $loaded = $loader->load();

        $this->assertArrayHasKey('foo', $loaded);
        $this->assertArrayHasKey('bar', $loaded);
    }
}
