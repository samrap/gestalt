<?php

namespace Gestalt\Loaders;

class PhpDirectoryLoader extends DirectoryLoader
{
    /**
     * The file extension that each file must have to be loaded.
     *
     * @var string
     */
    protected $extension = 'php';

    /**
     * Define the method of translating the current file into a configuration.
     *
     * @param  string $filePath
     * @return mixed
     */
    public function translateFile($filePath)
    {
        return require $filePath;
    }
}
