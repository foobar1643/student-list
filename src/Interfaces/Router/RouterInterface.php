<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Interfaces\Router
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Interfaces\Router;

/**
 * @todo Think about request method validation, is this a router's task?
 */
interface RouterInterface
{
    /**
     * Loads routes from given file.
     *
     * @throws \InvalidArgumentException If a file is not found.
     * @throws \RuntimeException If an error occured while reading a file.
     *
     * @param string $file Name of the file (with path) that contains routes.
     *
     * @return void
     */
    //public function loadFromFile($file);

    /**
     * Loads routes from PHP array.
     *
     * @return void
     */
    //public function loadFromArray(array $routes);

    /**
     * Creates a route for given path and method.
     *
     * @param string $path Route path.
     * @param string $method Route method.
     * @param callable $closure A closure to call when processing a route.
     *
     * @throws \InvalidArgumentException If a route with given path and method already exists.
     *
     * @return void
     */
    public function map($path, $method, callable $closure);

    /**
     * Checks if a route with a given path exists.
     *
     * @param  string  $path Route's path to check.
     *
     * @return boolean True if a route with a given path exists, false otherwise.
     */
    //public function hasRoute($path);

    /**
     * Checks if given request method is valid for a given route's path.
     *
     * Throws InvalidArgumentException if a route with given path does not exists.
     *
     * @param string $method Request method to check.
     * @param string $path Route's path.
     *
     * @throws \InvalidArgumentException if a route with given path does not exists.
     *
     * @return boolean True if request method is valid, false otherwise.
     */
    //public function requestMethodValid($method, $path);

    /**
     * Gets request method for a route with given path.
     *
     * Throws InvalidArgumentException if a route with given path does not exists.
     *
     * @param string $path Route's path.
     *
     * @throws \InvalidArgumentException if a route with given path does not exists.
     *
     * @return string Request method for a route with given path.
     */
    //public function getMethod($path);

    /**
     * Gets closure for a route with given path.
     *
     * Throws InvalidArgumentException if a route with given path does not exists.
     *
     * @param string $path Route's path.
     *
     * @throws \InvalidArgumentException if a route with given path does not exists.
     *
     * @return callable Closure for a route with given path.
     */
    //public function getClosure($path);

    /**
     * Should return an associative array, it which key being placeholder name, and value
     * being placeholder value respectively. Should return an empty array if no placeholders
     * was found in given path.
     *
     * @param string $path Path in which to look for placeholders
     *
     * @return array Associative array with placeholders name and values.
     */
    //public function getPlaceholders($path);
}
