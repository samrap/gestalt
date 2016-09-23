<?php

namespace Gestalt\Loaders;

class IniDirectoryLoader extends DirectoryLoader
{
    /**
     * The file extension that each file must have to be loaded.
     *
     * @var string
     */
    protected $extension = 'ini';

    /**
     * Define the method of translating the current file into a configuration.
     *
     * @param  string $filePath
     * @return mixed
     */
    public function translateFile($filePath)
    {
        return parse_ini_file($filePath, true);
    }
}
