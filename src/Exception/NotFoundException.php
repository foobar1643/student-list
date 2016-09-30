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
 * Exception thrown if a route is not found on the server.
 */
class NotFoundException extends ApplicationException
{
    /**
     * Constructor.
     *
     * @param string $path Path that were not found.
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            "Route for '{$request->getRequestTarget()}' is not found.",
            404,
            "The requested page is not found on this server."
            ." Please check the URL for typos or contact server administrator for more info.");
    }
}