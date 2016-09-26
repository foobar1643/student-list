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
 * @todo Name for routes?
 * @todo Create a simplier way to get a $path value. Maybe pass Request instance
 * to router?
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
    //public function loadFromFile($file)
    //{

    //}

    /**
     * Loads routes from PHP array.
     *
     * @return void
     */
    //public function loadFromArray(array $routes)
    //{

    //}

    /**
     * Creates a route for given path and method.
     *
     * @param string $path Route path.
     * @param string|array $methods Route methods. Route request methods.
     * @param callable $closure A closure to call when processing a route.
     *
     * @throws \InvalidArgumentException If a route with given path and method already exists.
     *
     * @return void
     */
    public function map($path, $methods, callable $closure)
    {
        $methods = !is_array($methods) ? [$methods] : $methods;
        $route = new Route($path, $methods, $closure);
        if($this->routeCollection->hasRoute($route)) {
            throw new \InvalidArgumentException("Route with path {$route->getPath()} already exist.");
        }
        $this->routeCollection->addRoute($route);
    }

    /**
     * Routes given request. On success returns a callable for a route.
     * Throws NotFoundException or NotAllowedException on failiure.
     *
     * @todo Throw NotAllowed exception if request method differs from the one mapped to route
     *
     * @param ServerRequestInterface $request Request to route.
     *
     * @return callable Callable for matched route.
     */
    public function routeRequest(ServerRequestInterface $request)
    {
        // Get request target
        $target = $request->getRequestTarget();
        // Get request method
        $method = $request->getMethod();
        // Get route for the request target
        $route = $this->routeCollection->getRouteForTarget($target, $method);
        // If there is no route for this request target
        if(is_null($route)) {
            // Throw a NotFoundException
            throw new NotFoundException($target);
        }
        return $route->getClosure();
    }

    /**
     * Returns given Request instance with modified attributes, in which
     * attribute key being route placeholder name, and attribute value being
     * route placeholder value respectively. This method does not modify request
     * attributes if no placeholders was found in the path.
     *
     * @param ServerRequestInterface $request Request instance to get placeholders from.
     *
     * @return ServerRequestInterface Request instance with filled attributes.
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