<?php

use Gestalt\Loaders\JsonDirectoryLoader;

class JsonDirectoryLoaderTest extends TestCase
{
    public function test_load_method_returns_configuration_array()
    {
        $loader = new JsonDirectoryLoader(__DIR__.'/config');
        $loadedConfig = $loader->load();

        // Checks the file name is the main key
        $this->assertArrayHasKey('foobar', $loadedConfig);

        // Checks that the value in the config is decoded
        $this->assertTrue($loadedConfig['foobar']->foobar);
    }
}
