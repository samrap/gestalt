<?php

namespace Gestalt\Loaders;

class JsonDirectoryLoader extends DirectoryLoader
{
    /**
     * The file extension that each file must have to be loaded.
     *
     * @var string
     */
    protected $extension = 'json';

    /**
     * Define the method of translating the current file into a configuration.
     *
     * @param  string $filePath
     * @return mixed
     */
    public function translateFile($filePath)
    {
        return json_decode(file_get_contents($filePath), true);
    }
}
