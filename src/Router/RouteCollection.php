<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Router
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Router;

use Students\Entity\Route;
use Students\Utility\Collection;
use Students\Interfaces\Router\RouteInterface;
use Students\Interfaces\Router\RouteCollectionInterface;

/**
 * A collection to store routes.
 *
 * @todo Think about using SplObjectStorage here.
 */
class RouteCollection extends Collection implements RouteCollectionInterface
{
    /**
     * Constructor.
     *
     * @todo Validate every element in the $routes array so nothing unwanted gets through.
     *
     * @param array $routes Routes to store in the collection.
     */
    public function __construct(array $routes)
    {
        parent::__construct($routes);
    }

    /**
     * This method does nothing in order to retain proper encapsulation.
     * Use addRoute() for route addition.
     */
    public function set($key, $value = null)
    {
        // Do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(Route $route)
    {
        if(parent::has($this->getStorageName($route))) {
            throw new \InvalidArgumentException("Route to '{$route->getPath()}'"
                ." with method(s) {$route->getMethodsAsString()} already exists.");
        }
        parent::set($this->getStorageName($route), $route);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutesForTarget($target)
    {
        $routes = [];
        foreach($this->data as $hash => $route) {
            if($route->matchPlaceholders($target)) {
                array_push($routes, $route);
            }
        }
        return $routes;
    }

    /**
     * Returns a md5 hash of route path and route methods.
     *
     * @param Route $route Route for which storage name will be generated.
     *
     * @return string md5 hash of route path and route methods.
     */
    protected function getStorageName(Route $route)
    {
        return md5($route->getPath() . $route->getMethodsAsString());
    }
}