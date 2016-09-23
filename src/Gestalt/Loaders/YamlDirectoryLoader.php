<?php

namespace Gestalt\Loaders;

class YamlDirectoryLoader extends DirectoryLoader
{
    /**
     * The file extension that each file must have to be loaded.
     *
     * @var string
     */
    protected $extension = 'yaml';

    /**
     * Define the method of translating the current file into a configuration.
     *
     * @param  string $filePath
     * @return mixed
     */
    public function translateFile($filePath)
    {
        return yaml_parse_file($filePath);
    }
}
