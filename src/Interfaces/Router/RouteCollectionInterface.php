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

use Students\Interfaces\CollectionInterface;
use Students\Entity\Route;

interface RouteCollectionInterface extends CollectionInterface
{

    /**
     * Adds a route to the collection.
     *
     * @param Route $route Route to add.
     */
    public function addRoute(Route $route);

    /**
     * Retrieves an array of routes for given target. If no routes was found
     * for given target, returns an empty array.
     *
     * @param string $target Target for matching the route.
     *
     * @return array Array of routes for given target. Empty array if no routes
     * was found for given target.
     */
    public function getRoutesForTarget($target);
}