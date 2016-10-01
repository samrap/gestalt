<?php

namespace Gestalt\Loaders;

use DirectoryIterator;

abstract class DirectoryLoader implements LoaderInterface
{
    /**
     * The directory to load INI configuration files from.
     *
     * @var array
     */
    protected $directory;

    /**
     * The file extension that each file must have to be loaded.
     *
     * @var string
     */
    protected $extension;

    /**
     * Create an IniDirectoryLoader instance.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Define the method of translating the current file into a configuration.
     *
     * @param  string $filePath
     * @return mixed
     */
    abstract public function translateFile($filePath);

    /**
     * Load the configuration items and return them as an array.
     *
     * @return array
     */
    public function load()
    {
        $items = [];
        $directory = new DirectoryIterator(realpath($this->directory));

        foreach ($directory as $file) {
            if ($file->isFile() && $file->getExtension() == $this->extension) {
                $filename = $file->getFilename();
                $config = substr($filename, 0, strrpos($filename, '.'));

                $items[$config] = $this->translateFile($file->getPathname());
            }
        }

        return $items;
    }
}
