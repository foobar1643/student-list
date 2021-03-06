<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Students\Http\Request;
use Students\Http\Response;
use Students\Http\Stream;
use Students\Http\Uri;
use Students\Http\Headers;
use Students\Interfaces\ConfigInterface;
use Students\Router\Router;
use Students\Exception\NotFoundException;
use Students\Exception\ApplicationException;

/**
 * Students application.
 */
class Application
{
    /**
     * Response chunk size in bytes.
     *
     * @var int
     */
    const RESPONSE_CHUNK_SIZE = 2048;

    /**
     * Application router object.
     *
     * @var \Students\Router\Router
     */
    protected $router;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Starts an application.
     */
    public function start()
    {
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        $request = Request::fromServer($_SERVER);
        $this->processRequest($request);
    }

    /**
     * Maps a route with a given path and method to the given closure.
     *
     * @param string $path Route path.
     * @param string $method Route method.
     * @param callable $handle Callable that will process the route.
     */
    public function route($path, $method, callable $handle)
    {
        $this->router->map($path, $method, $handle);
    }

    /**
     * Processes given PSR-7 Request instance.
     *
     * @param Request $request PSR-7 Reuqest instance to process.
     */
    protected function processRequest(Request $request)
    {
        // Create a Response that is going to be used for responding client.
        $response = $this->createResponse();
        // Route current request
        $callable = $this->router->routeRequest($request);
        // Call the callable mapped to the route
        $response = call_user_func($callable, $request, $response);
        // Check if callable returned a PSR-7 Response object
        if(!($response instanceof ResponseInterface)) {
            // If callable mapped to a route returned something other than
            // PSR-7 Resposnse object, throw RuntimeException.
            throw new \RuntimeException('Callable mapped to the route should always return an instance'
                .' of PSR-7 ResponseInterface.');
        }
        // Respond to the client using
        $this->respond($response);
    }

    /**
     * Responds to the client using given PSR-7 Response instance.
     *
     * @todo Separate function for printing response body.
     *
     * @param Response $response PSR-7 Response instance.
     */
    protected function respond(Response $response)
    {
        if(!headers_sent()) {
            // Send a header with protocol version, response status and reason phrase.
            header(sprintf("HTTP/%s %s %s",
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()));

            // Send response headers.
            foreach($response->getHeaders() as $header => $values) {
                foreach($values as $key => $value) {
                    header(sprintf("%s: %s", $header, $value), false);
                }
            }
        }

        // Print out response body
        $body = $response->getBody();
        $readAmount = $response->getHeader('Content-Length');
        // If body is seekable - rewind the pointer.
        if($body->isSeekable()) {
            $body->rewind();
        }

        if(isset($readAmount)) {
            while(!$body->eof()) {
                // Break the loop if connection is not normal
                if(connection_status() !== CONNECTION_NORMAL) {
                    break;
                }

                // Read fixed amout of bytes from the stream.
                print($body->read(self::RESPONSE_CHUNK_SIZE));
            }
        } else {
            while($readAmount > 0 && !$body->eof()) {
                // Break the loop if connection is not normal
                if(connection_status() !== CONNECTION_NORMAL) {
                    break;
                }

                $bytesRead = min(self::RESPONSE_CHUNK_SIZE, $readAmount);
                print($body->read($bytesRead));
                $readAmount -= $bytesRead;
            }
        }

    }

    /**
     * Creates an empty PSR-7 Response instance.
     *
     * @return \Psr\Http\Message\ResponseInterface Empty PSR-7 Response instance.
     */
    protected function createResponse()
    {
        $headers = new Headers([]);
        $body = new Stream(fopen('php://temp', 'w+'));
        $statusCode = 200;
        return new Response($headers, $body, $statusCode);
    }

    /**
     * Handles exceptions that were thrown in the application.
     *
     * @todo: Clear existing body before printing an exception body.
     *
     * @param mixed $exception Exception to handle.
     */
    public function exceptionHandler($exception)
    {
        $statusCode = ($exception instanceof ApplicationException) ? $exception->getHttpStatusCode() : 503;
        $response = new Response(new Headers([]), new Stream(fopen('php://temp', 'w+')), $statusCode);
        ob_start();
        require(__DIR__ . "/../templates/error.phtml"); # Include default error template
        $body = $response->getBody();
        $body->write(ob_get_clean());
        ob_end_clean();
        $this->respond($response->withBody($body));
    }

    /**
     * Turns PHP errors into ErrorExceptions. If error reporting is not enabled,
     * returns nothing.
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}