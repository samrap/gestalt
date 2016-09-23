<?php

use Gestalt\Loaders\YamlDirectoryLoader;

class YamlDirectoryLoaderTest extends TestCase
{
    public function test_load_method_returns_configuration_array()
    {
        $loader = new YamlDirectoryLoader(__DIR__.'/config');
        $loaded = $loader->load();

        $this->assertArrayHasKey('foobar', $loaded);
    }
}
