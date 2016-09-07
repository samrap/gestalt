<?php

use Gestalt\Loaders\IniDirectoryLoader;

class IniDirectoryLoaderTest extends PHPUnit_Framework_TestCase
{
    public function test_load_method_returns_configuration_array()
    {
        $loader = new IniDirectoryLoader(__DIR__.'/ini');
        $loaded = $loader->load();

        $this->assertArrayHasKey('foobar', $loaded);
    }
}
