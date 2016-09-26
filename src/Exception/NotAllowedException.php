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
 * Exception thrown if a request method is not allowed at the location.
 */
class NotAllowedException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $method Method that were not allowed.
     */
    public function __construct($method)
    {
        parent::__construct("Request method '{$method}' is not allowed at this location.");
    }
}