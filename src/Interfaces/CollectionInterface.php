<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Interfaces
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Interfaces;

/**
 * Describes simple collection of data.
 *
 * Excends ArrayAcces and Countable interfaces.
 */
interface CollectionInterface extends \ArrayAccess, \Countable
{
    /**
     * Returns a key's value from the collection.
     * If a given key does not exist in the collection, returns a specified default value.
     *
     * @param string $key A key to look for in the collection.
     * @param mixed $default A value to be returned if key does not exists in the collection.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Adds a value with a given key to the collection.
     *
     * @param string $key A key in the collection.
     * @param mixed $value A value to add to the collection.
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Checks if the collection has an element with a given key.
     *
     * @param string $key A key to check.
     *
     * @return bool
     */
    public function has($key);

    /**
     * Returns all items from the collection.
     *
     * @return array
     */
    public function all();

    /**
     * Removes an element with a given key from the collection.
     *
     * @param string $key A key of the element to remove.
     *
     * @return void
     */
    public function remove($key);

    /**
     * Removes all items from the collection.
     *
     * @return void
     */
    public function clear();
}