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
     * Application configuration object.
     *
     * @var \Students\Interfaces\ConfigInterface
     */
    protected $config;

    /**
     * Application router object.
     *
     * @var \Students\Router\Router
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param ConfigInterface $config Application configuration object.
     */
    public function __construct()
    {
        //$this->config = $config;
        $this->router = new Router();
    }

    /**
     * Starts an application.
     *
     * @todo Think about better way to retrieve HTTP request headers.
     *
     * @return void
     */
    public function start()
    {
        $request = Request::fromServer($_SERVER);
        $this->processRequest($request);
    }

    /**
     * Maps a route with a given path and method to the given closure.
     *
     * @param string $path Route path.
     * @param string $method Route method.
     * @param callable $handle Callable that will process the route.
     *
     * @return void
     */
    public function route($path, $method, callable $handle)
    {
        $this->router->map($path, $method, $handle);
    }

    protected function processRequest(Request $request)
    {
        $path = $request->getRequestTarget();
        $response = $this->createResponse();
        // Route current request
        $closure = $this->router->routeRequest($request);
        $response = $closure($request, $response);
        // Check if handle has retutned a response object
        if(!($response instanceof ResponseInterface)) {
            // If no response was returned - throw a RuntimeException
            throw new \RuntimeException('Mapped closure should return a Response object.');
        }
        // If a handle has returned response object - call respond() method
        $this->respond($response);
    }


    /**
     * @todo Separate function for printing response body.
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

    protected function createResponse()
    {
        $headers = new Headers([]);
        $body = new Stream(fopen('php://temp', 'w+'));
        $statusCode = 200;
        return new Response($headers, $body, $statusCode);
    }

    protected function exceptionHandler($exception)
    {

    }
}