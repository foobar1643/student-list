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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An abstract class that implements PSR-7 MessageInterface.
 *
 * Acts as a template to PSR-7 Request and Response classes.
 *
 * @link http://www.php-fig.org/psr/psr-7/#3-1-psr-http-message-messageinterface
 */
abstract class Message implements MessageInterface
{
    /**
     * HTTP protocol version.
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * An array of allowed HTTP protocol versions.
     * @var string[]
     */
    protected $allowedProtocolVersions = ['1.0', '1.1', '2.0'];

    /**
     * Collection of HTTP headers.
     * @var \Students\Interfaces\Http\HeadersCollectionInterface
     */
    protected $headers;

    /**
     * Message body represented as a stream.
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * @param string $version HTTP protocol version.
     *
     * @throws \InvalidArgumentException If specified protocol version is not in the allowed protocol versions array.
     *
     * @return static
     */
    public function withProtocolVersion($version)
    {
        if(!in_array($this->allowedProtocolVersions, $version)) {
            throw new \InvalidArgumentException("Given HTTP protocol version is not allowed.");
        }
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     * While header names are not case-sensitive, this method will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders()
    {
        return $this->headers->all();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return $this->headers->has($name);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method returns an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string[]
     */
    public function getHeader($name)
    {
        return $this->headers->get($name, []);
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method returns
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string
     */
    public function getHeaderLine($name)
    {
        return $this->hasHeader($name) ? implode(",", $this->getHeader($name)) : '';
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @throws \InvalidArgumentException for invalid header names or values.
     *
     * @return static
     */
    public function withHeader($name, $value)
    {
        if(!is_string($name)) {
            throw new \InvalidArgumentException('Can\'t replace header. Header name must be a string.');
        }
        $clone = clone $this;
        $clone->headers->set($name, $value);
        return $clone;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @throws \InvalidArgumentException for invalid header names or values.
     *
     * @return static
     */
    public function withAddedHeader($name, $value)
    {
        if(!is_string($name)) {
            throw new \InvalidArgumentException('Can\'t append to header. Header name must be a string.');
        }
        $clone = clone $this;
        $clone->headers->add($name, $value);
        return $clone;
    }

    /**
     * Return an instance without the specified header.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @throws \InvalidArgumentException for invalid header names.
     *
     * @return static
     */
    public function withoutHeader($name)
    {
        if(!is_string($name)) {
            throw new \InvalidArgumentException('Can\'t remove header. Header name must be a string.');
        }
        $clone = clone $this;
        $clone->headers->remove($name);
        return $clone;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * @todo Check for invalid body.
     *
     * @param StreamInterface $body Body.
     *
     * @throws \InvalidArgumentException When the body is not valid.
     *
     * @return static
     */
    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }
}