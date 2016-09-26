<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Exception
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Exception;

/**
 * Exception thrown if a route is not found on the server.
 */
class NotFoundException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $path Path that were not found.
     */
    public function __construct($path)
    {
        parent::__construct("Path '{$path}' is not found on this server.");
    }
}