<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Utility
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Utility;

use Students\Interfaces\CollectionInterface;

/**
 * Provides simple collection of data.
 *
 * Implements ArrayAcces and Countable interfaces.
 */
class Collection implements CollectionInterface
{
    /**
     * Array of data stored in the collection.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param array $data Data to store in the collection.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns a key's value from the collection.
     *  If a given key does not exist in the collection,
     *  returns a specified default value.
     *
     * @param string $key A key to look for in the collection.
     * @param mixed $default A value to be returned if key does not exists in the collection.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * Adds a value with a given key to the collection.
     *
     * @param string $key A key in the collection.
     * @param mixed $value A value to add to the collection.
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Checks if the collection has an element with a given key.
     *
     * @param string $key A key to check.
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Removes an element with a given key from the collection.
     *
     * @param string $key A key of the element to remove.
     *
     * @return void
     */
    public function remove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Returns all items from the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Removes all items from the collection.
     *
     * @return void
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * Counts elements in the collection.
     *
     * Implementation of a Countable interface.
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Checks if the collection has a given key.
     *
     * Implementation of a ArrayAccess interface.
     *
     * @param string $offset A key to check.
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Returns a key's value from the collection.
     *
     * Implementation of a ArrayAccess interface.
     *
     * @param string $offset A key to check.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Adds a value with a given key to the collection.
     *
     * Implementation of a ArrayAccess interface.
     *
     * @param string $offset A key to check.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Removes an element with a given key from the collection.
     *
     * Implementation of a ArrayAccess interface.
     *
     * @param string $offset A key to remove
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}