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
 */
class RouteCollection extends Collection implements RouteCollectionInterface
{
    public function addRoute(Route $route)
    {
        parent::set($this->getStorageName($route), $route);
    }

    public function getRouteForTarget($target, $method)
    {
        foreach($this->data as $name => $route) {
            if($route->matchPlaceholders($target) && $route->hasMethod($method)) {
                return $route;
            }
        }
        return null;
    }

    public function hasRoute(Route $route)
    {
        $originalRoute = parent::get($this->getStorageName($route));
        if(!is_null($originalRoute)) {
            if($originalRoute->getMethods() === $route->getMethods()) {
                return true;
            }
        }
        return false;
    }

    protected function getStorageName(Route $route)
    {
        return md5($route->getPath() . implode($route->getMethods(), ','));
    }
}