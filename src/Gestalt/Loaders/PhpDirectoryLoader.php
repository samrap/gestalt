<?php

namespace Gestalt\Loaders;

use DirectoryIterator;

class PhpDirectoryLoader implements LoaderInterface
{
    /**
     * The directory to load PHP configuration files from.
     *
     * @var array
     */
    protected $directory;

    /**
     * Create a PhpDirectoryLoader instance.
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
            if ($file->isFile() && $file->getExtension() == 'php') {
                $filename = $file->getFilename();
                $config = substr($filename, 0, strrpos($filename, '.'));

                $items[$config] = require $file->getPathname();
            }
        }

        return $items;
    }
}
