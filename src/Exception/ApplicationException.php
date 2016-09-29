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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Students\Http\Stream;
use Students\Http\Headers;
use Students\Http\Response;
use Students\Interfaces\Http\HeadersCollectionInterface;

/**
 * Exception thrown if something goes wrong in the application.
 */
class ApplicationException extends \Exception
{

    /**
     * Status code for HTTP response
     * @var int
     */
    protected $statusCode;

    /**
     * Error description.
     * @var string
     */
    protected $description;

    /**
     * Constructor.
     *
     * @param string $method Method that were not allowed.
     */
    public function __construct(
        $message,
        $statusCode = 503,
        $description = 'A server error occured. Please try again after some time or contact server administrator.'
    )
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->description = $description;
    }

    /**
     * Retrieves Exception HTTP response status code.
     *
     * @return int Status code for HTTP response.
     */
    public function getHttpStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Retrieves custom description for current exception.
     *
     * @return string Custom description message.
     */
    public function getCustomDescription()
    {
        return $this->description;
    }
}