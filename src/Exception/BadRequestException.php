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
 * Exception thrown if the server cannot or will not process the request due to
 * an apparent client error.
 */
class BadRequestException extends ApplicationException
{
    /**
     * Constructor.
     *
     * @param string $method Method that were not allowed.
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            "Bad request with '{$request->getMethod()}' method, at '{$request->getRequestTarget()}' location.",
            400,
            "It seems that your request is malformed in some way."
            ." Try again or contact server administrator for more info.");
    }
}