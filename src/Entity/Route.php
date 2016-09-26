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

use Students\Interfaces\Router\RouteInterface;

/**
 * Route entity.
 *
 * Implements RouteInterface
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
     * @var string
     */
    protected $methods;

    /**
     * Closure to call for route processing.
     * @var \Closure
     */
    protected $handle;

    /**
     * Constructor.
     *
     * @param string $path Route destination path.
     * @param string $method Request method for a route.
     * @param \Closure $handle Closure to call for processing.
     */
    public function __construct($path, $methods, $handle)
    {
        $this->path = $path;
        $this->methods = $methods;
        $this->handle = $handle;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getClosure()
    {
        return $this->handle;
    }

    public function hasMethod($method)
    {
        return in_array($method, $this->methods);
    }

    public function matchPlaceholders($path)
    {
        $preparedRoute = $this->prepareRoute();
        return boolval(preg_match($preparedRoute, $path));
    }

    public function getPlaceholders($path)
    {
        $names = $this->getPlaceholderNames();
        $values = $this->getPlaceholderValues($this->prepareRoute(), $path);
        return (!empty($names) && count($names) === count($values)) ? array_combine($names, $values) : null;
    }

    protected function getPlaceholderNames()
    {
        // Match everything that is enclosed in brackets (without brackets).
        preg_match_all('/[(]([a-zA-Z]+)[)]/', $this->path, $matches);
        return $matches[1];
    }

    protected function prepareRoute()
    {
        // Match everything that is enclosed in brackets (brackets included) and replace it PLACEHOLDER text
        $placeholderRoute = preg_replace('/([(][a-zA-Z]+[)])/', 'PLACEHOLDER', $this->path);
        // Quote a route so it can be used in regular expression
        $placeholderRoute = "/^" . preg_quote($placeholderRoute, '/') . "$/";
        // Finalize quoted route by replacing all PLACEHOLDERs with regular expressions.
        return str_replace('PLACEHOLDER', '([a-z-A-Z0-9]+)', $placeholderRoute);
    }

    protected function getPlaceholderValues($regex, $path)
    {
        // Using created regular expression, find out if current path matches the route
        preg_match($regex, $path, $routePlaceholders);
        // Splice the full match string from resulting array
        return array_splice($routePlaceholders, 1);
    }
}