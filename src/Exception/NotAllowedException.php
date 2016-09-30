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

use Psr\Http\Message\ServerRequestInterface;

/**
 * Exception thrown if a request method is not allowed at the location.
 */
class NotAllowedException extends ApplicationException
{
    /**
     * Constructor.
     *
     * @param string $method Method that were not allowed.
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            "Method '{$request->getMethod()}' is not allowed at the '{$request->getRequestTarget()}' location.",
            405,
            "A request method is not supported for the requested page."
            ." Try again or contact server administrator for more info.");
    }
}