<?php

namespace Gestalt\Loaders;

use DirectoryIterator;

class YamlDirectoryLoader implements LoaderInterface
{
    /**
     * The directory to load INI configuration files from.
     *
     * @var array
     */
    protected $directory;

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
     * Load the configuration items and return them as an array.
     *
     * @return array
     */
    public function load()
    {
        $items = [];
        $directory = new DirectoryIterator(realpath($this->directory));

        foreach ($directory as $file) {
            if ($file->isFile() && $file->getExtension() == 'yaml') {
                $filename = $file->getFilename();
                $config = substr($filename, 0, strrpos($filename, '.'));

                $items[$config] = yaml_parse_file($file->getPathName());
            }
        }

        return $items;
    }
}
