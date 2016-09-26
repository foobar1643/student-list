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

interface RouteInterface
{
    public function getPath();

    public function getMethod();

    public function getClosure();
}
