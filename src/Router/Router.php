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

use Students\Interfaces\Router\RouterInterface;
use Students\Entity\Route;
use Students\Exception\NotFoundException;
use Students\Exception\NotAllowedException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Loads and stores routes.
 *
 * @todo Named routes?
 * @todo Think about better and faster way to find routes
 */
class Router implements RouterInterface
{
    /**
     * Collection that stores routes.
     *
     * @var \Students\Router\RouteCollectionInterface
     */
    protected $routeCollection;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->routeCollection = new RouteCollection([]);
    }

    # public function public function loadFromFile($file)

    # public function loadFromArray(array $routes)

    /**
     * {@inheritdoc}
     */
    public function map($path, $methods, callable $closure)
    {
        $methods = !is_array($methods) ? [$methods] : $methods;
        $route = new Route($path, $methods, $closure);
        // Will throw InvalidArgumentException if a route with same path and method(s)
        // is already in the collection.
        $this->routeCollection->addRoute($route);
    }

    /**
     * {@inheritdoc}
     */
    public function routeRequest(ServerRequestInterface $request)
    {
        // Get request target
        $target = $request->getRequestTarget();
        // Get request method
        $method = $request->getMethod();
        // Get all routes for the request target
        $routes = $this->routeCollection->getRoutesForTarget($target);
        // If there is no routes for this request target
        if(empty($routes)) {
            // Throw a NotFoundException
            throw new NotFoundException($request);
        }

        foreach($routes as $key => $route) {
            if($route->hasMethod($method)) {
                return $route->getClosure();
            }
        }

        throw new NotAllowedException($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestPlaceholders(ServerRequestInterface $request)
    {
        $placeholders = $this->getPlaceholders($request->getRequestTarget());
        return $request->withAttributes($placeholders);
    }

    /**
     * Returns an associative array, it which key being placeholder name, and value
     * being placeholder value respectively. Returns an empty array if no placeholders
     * was found in given path.
     *
     * @param string $path Path in which to look for placeholders
     *
     * @return array Associative array with placeholders name and values.
     */
    protected function getPlaceholders($path)
    {
        foreach($this->routeCollection->all() as $name => $route) {
            if(($placeholders = $route->getPlaceholders($path)) !== null) {
                return $placeholders;
            }
        }
        return [];
    }
}