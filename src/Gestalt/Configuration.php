<?php

namespace Gestalt;

use Closure;
use ArrayAccess;
use Traversable;
use Gestalt\Util\Observable;
use Gestalt\Loaders\LoaderInterface;

class Configuration extends Observable implements ArrayAccess
{
    /**
     * The configuration items.
     *
     * @var array
     */
    protected $items;

    /**
     * The original configuration items.
     *
     * @var array
     */
    protected $original;

    /**
     * Create a new Configuration instance.
     *
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        $this->items = $this->getItemsAsArray($items);

        $this->original = $this->items;
    }

    /**
     * Create a new Configuration with the given loader.
     *
     * @param  \Gestalt\Loaders\LoaderInterface|\Closure $loader
     * @return \Gestalt\Collection
     */
    public static function create($loader)
    {
        if ($loader instanceof Closure) {
            return new self($loader());
        } elseif ($loader instanceof LoaderInterface) {
            return new self($loader->load());
        }
    }

    /**
     * Create a Configuration instance from a LoaderInterface's `load` method.
     *
     * @deprecated 1.0.0 Replaced with more flexible `create` method.
     * @param  \Gestalt\Loaders\LoaderInterface $loader
     * @return \Gestalt\Configuration
     */
    public static function fromLoader(LoaderInterface $loader)
    {
        // We will create a new instance using `self`, as we do not want child
        // classes creating new instances of themselves if this is called.
        return new self($loader->load());
    }

    /**
     * Convert the given items into an array.
     *
     * @param  mixed $items
     * @return array
     */
    protected function getItemsAsArray($items)
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof self) {
            return $items->all();
        } elseif ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array) $items;
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
            if (is_array($result) && array_key_exists($piece, $result)) {
                $result = $result[$piece];
            } else {
                return;
            }
        }

        return $result;
    }

    /**
     * Determine if the specified item exists in the configuration.
     *
     * @param  string $key
     * @return bool
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
        $keys = explode('.', $key);
        $section = &$this->items;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($section[$key])) {
                // If the key does not exist, we will set it to an empty array
                // and move into the next dimension.
                $section[$key] = [];
            } elseif (! is_array($section[$key])) {
                // If the item at this dimension is not an array, then we would
                // be overriding it if we continued any further. As the great
                // master programmer Yoda once said, exit we must.
                return;
            }

            $section = &$section[$key];
        }

        $key = array_shift($keys);

        if (! array_key_exists($key, $section)) {
            $section[$key] = $value;

            $this->notify();
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
        $keys = explode('.', $key);
        $section = &$this->items;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($section[$key]) || ! is_array($section[$key])) {
                // If the key does not exist, we will set it to an empty array
                // and move into the next dimension.
                $section[$key] = [];
            }

            $section = &$section[$key];
        }

        $section[array_shift($keys)] = $value;

        $this->notify();
    }

    /**
     * Remove an item from the configuration.
     *
     * @param  string $key
     * @return void
     */
    public function remove($key)
    {
        $keys = explode('.', $key);
        $section = &$this->items;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($section[$key])) {
                return;
            }

            $section = &$section[$key];
        }

        unset($section[array_shift($keys)]);
    }

    /**
     * Reset the configuration items to the original values.
     *
     * @return \Gestalt\Collection
     */
    public function reset()
    {
        $this->items = $this->original;

        return $this;
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
     * @return bool
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
