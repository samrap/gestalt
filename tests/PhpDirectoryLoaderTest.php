<?php

use Gestalt\Loaders\PhpDirectoryLoader;

class PhpDirectoryLoaderTest extends TestCase
{
    public function test_load_method_returns_configuration_array()
    {
        $loader = new PhpDirectoryLoader(__DIR__.'/config');
        $loaded = $loader->load();

        $this->assertArrayHasKey('foo', $loaded);
        $this->assertArrayHasKey('bar', $loaded);
    }
}
