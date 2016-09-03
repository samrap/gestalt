<?php

namespace Gestalt;

use ArrayAccess;
use Gestalt\Loaders\LoaderInterface;

class Configuration implements ArrayAccess
{
    /**
     * The configuration items.
     *
     * @var array
     */
    protected $items;

    /**
     * Create a new Configuration instance.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Create a Configuration instance from a LoaderInterface's `load` method.
     *
     * @param  LoaderInterface $loader
     * @return \Gestalt\Configuration
     */
    public static function fromLoader(LoaderInterface $loader)
    {
        // We will create a new instance using `self`, as we do not want child
        // classes creating new instances of themselves if this is called.
        return new self($loader->load());
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Get a configuration item.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            return $this->items[$key];
        }

        $result = $this->items;

        foreach (explode('.', $key) as $piece) {
            if (array_key_exists($piece, $result)) {
                $result = $result[$piece];
            } else {
                return null;
            }
        }

        return $result;
    }

    /**
     * Determine if the specified item exists in the configuration.
     *
     * @param  string $key
     * @return boolean
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Add an item to the configuration if it does not already exist.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add($key, $value)
    {
        if (! $this->exists($key)) {
            $this->items[$key] = $value;
        }
    }

    /**
     * Add or modify an item in the configuration.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * Remove an item from the configuration.
     *
     * @param  string $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Add or modify an item in the configuration.
     *
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * Determine if the configuration item exists at the given offset.
     *
     * @param  string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * Remove an item from the configuration.
     *
     * @param  string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Get a configuration item.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}
