<?php

use Gestalt\Loaders\JsonDirectoryLoader;

class JsonDirectoryLoaderTest extends TestCase
{
    public function test_load_method_returns_configuration_array()
    {
        $loader = new JsonDirectoryLoader(__DIR__.'/config');
        $loaded = $loader->load();

        $this->assertArrayHasKey('foobar', $loaded);
    }
}
