<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Entity
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Entity;

/**
 * Route entity.
 */
class Route
{
    /**
     * Route destination.
     * @var string
     */
    protected $path;

    /**
     * Array with route methods.
     * @var array
     */
    protected $methods;

    /**
     * Callable to call for route processing.
     * @var \Closure
     */
    protected $callable;

    /**
     * Constructor.
     *
     * @param string $path Route destination.
     * @param array $methods Request methods for a route.
     * @param \Closure $callable Callable to call for processing.
     */
    public function __construct($path, $methods, $callable)
    {
        $this->path = $path;
        $this->methods = $methods;
        $this->callable = $callable;
    }

    /**
     * Retrieves route destination.
     *
     * @return string Route destination.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieves request methods for a route.
     *
     * @return array Request methods for a route.
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Retrieves mapped callable, that is used for processing the route.
     * @return \Closure Callable to call for processing the route.
     */
    public function getClosure()
    {
        return $this->callable;
    }

    /**
     * Checks if a route supports method with a given name.
     *
     * @param string $method HTTP Request method name to check.
     *
     * @return boolean True if a route supprots request method, false if otherwise.
     */
    public function hasMethod($method)
    {
        return in_array($method, $this->methods);
    }

    /**
     * Returns route methods as a string, concatenated with a comma.
     *
     * @return string Route methods concatenated with a comma.
     */
    public function getMethodsAsString()
    {
        implode($this->getMethods(), ',');
    }

    /**
     * Checks if given path value matches route placeholders.
     *
     * @param string $path Path to check.
     *
     * @return boolean True if given path matches route placeholders, false otherwise.
     */
    public function matchPlaceholders($path)
    {
        $preparedRoute = $this->prepareRoute();
        return boolval(preg_match($preparedRoute, $path));
    }

    /**
     * Returns an associative array in which keys being placeholder names, and values
     * being placeholders values respectively. Returns null if given path does not
     * match the route.
     *
     * @param string $path Path to extract placeholder values from.
     *
     * @return array|null Array with placeholder names and values, null on failure.
     */
    public function getPlaceholders($path)
    {
        $names = $this->getPlaceholderNames();
        $values = $this->getPlaceholderValues($this->prepareRoute(), $path);
        return (!empty($names) && count($names) === count($values)) ? array_combine($names, $values) : null;
    }

    /**
     * Extracts placeholder names (if any) from the route destination.
     *
     * @return array Array with placeholder names.
     */
    protected function getPlaceholderNames()
    {
        // Match everything that is enclosed in brackets (without brackets).
        preg_match_all('/[(]([a-zA-Z]+)[)]/', $this->path, $matches);
        return $matches[1];
    }

    /**
     * Prepares the route for regular expression matching, replacing everything that
     * enclosed in brackets with regular expressions.
     *
     * @return string Route target, prepared for regular expression matching.
     */
    protected function prepareRoute()
    {
        // Match everything that is enclosed in brackets (brackets included) and replace it PLACEHOLDER text
        $placeholderRoute = preg_replace('/([(][a-zA-Z]+[)])/', 'PLACEHOLDER', $this->path);
        // Quote a route so it can be used in regular expression
        $placeholderRoute = "/^" . preg_quote($placeholderRoute, '/') . "$/";
        // Finalize quoted route by replacing all PLACEHOLDERs with regular expressions.
        return str_replace('PLACEHOLDER', '([a-z-A-Z0-9]+)', $placeholderRoute);
    }

    /**
     * Extracts route placeholder values by matching given path against given regular
     * expression.
     *
     * @param string $regex Regular expression to use.
     * @param string $path Path from which to extract placeholder values.
     *
     * @return array Array with placeholder values.
     */
    protected function getPlaceholderValues($regex, $path)
    {
        // Using created regular expression, find out if current path matches the route
        preg_match($regex, $path, $routePlaceholders);
        // Splice the full match string from resulting array
        return array_splice($routePlaceholders, 1);
    }
}