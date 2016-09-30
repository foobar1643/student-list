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

use Psr\Http\Message\ServerRequestInterface;

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
    #public function loadFromFile($file);

    /**
     * Loads routes from PHP array.
     *
     * @return void
     */
    #public function loadFromArray(array $routes);

    /**
     * Creates a route for given path and method.
     *
     * @param string $path Route destination.
     * @param string|array $methods Route request method(s).
     * @param callable $closure A callable to call when processing a route.
     *
     * @throws \InvalidArgumentException If a route with given path and method(s) is
     * already in the collection.
     */
    public function map($path, $method, callable $closure);

    /**
     * Routes given request. On success returns a callable for a route.
     * Throws NotFoundException or NotAllowedException on failure.
     *
     * @todo Maybe this should return a Route entity, rather than just callable?
     *
     * @param ServerRequestInterface $request Request to route.
     *
     * @throws \Students\Exception\NotFoundException If a route for request target is not found.
     * @throws \Students\Exception\NotAllowedException If request method is not allowed for current route.
     *
     * @return callable Callable that is mapped to the found route.
     */
    public function routeRequest(ServerRequestInterface $request);

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
    public function getRequestPlaceholders(ServerRequestInterface $request);
}
