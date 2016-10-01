<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Http
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Students\Interfaces\Http\HeadersCollectionInterface;

/**
 * Representation of an outgoing, server-side response according to PSR-7.
 *
 * Extends Message.
 *
 * @link http://www.php-fig.org/psr/psr-7/#3-3-psr-http-message-responseinterface
 */
class Response extends Message implements ResponseInterface
{
    /**
     * Request status code.
     * @var int
     */
    protected $statusCode;

    /**
     * Reason phrase for current status code.
     * @var string
     */
    protected $reasonPhrase;

    /**
     * Default reason phrases for Response status codes according to RFC 7231.
     * @see https://tools.ietf.org/html/rfc7231#section-6
     * @var array
     */
    protected $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ];

    /**
     * Constructor.
     *
     * @param HeadersCollectionInterface $headers Response headers.
     * @param StreamInterface $body Response body.
     * @param int $statusCode Response status code.
     * @param string $reasonPhrase Response reason phrase.
     */
    public function __construct(
        HeadersCollectionInterface $headers,
        StreamInterface $body,
        $statusCode,
        $reasonPhrase = '')
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->statusCode = $this->filterStatusCode($statusCode);
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $clone = clone $this;
        $clone->statusCode = $this->filterStatusCode($code);
        $clone->reasonPhrase = $this->filterReasonPhrase($code, $reasonPhrase);
        return $clone;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Filters status code, returns the code if it is correct.
     *
     * @param int $code Status code to filter.
     *
     * @throws InvalidArgumentException If status code is invalid.
     *
     * @return int Filtered status code.
     */
    protected function filterStatusCode($code)
    {
        if(!is_int($code) || !array_key_exists($code, $this->phrases)) {
            throw new \InvalidArgumentException('Status code must be valid according to RFC 7231.');
        }
        return $code;
    }

    /**
     * Filters reason phrase, returns the reason phrase if it is correct.
     * Otherwise, returns a default reason phrase for given status code.
     *
     * @param int $statusCode HTTP response status code.
     * @param string $phrase Reason phrase to filter.
     *
     * @return string Filtered reason phrase.
     */
    protected function filterReasonPhrase($statusCode, $phrase)
    {
        return (empty($phrase)) ? $this->phrases[$statusCode] : $phrase;
    }
}