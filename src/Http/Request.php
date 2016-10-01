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

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestInterface;
use Students\Exception\NotImplementedException;
use Students\Utility\Collection;
use Students\Interfaces\Http\HeadersCollectionInterface;
use Students\Utility\StringUtils;

/**
 * Representation of an incoming, server-side HTTP request according to PSR-7.
 *
 * Extends message.
 *
 * @link http://www.php-fig.org/psr/psr-7/#3-2-psr-http-message-requestinterface
 * @link http://www.php-fig.org/psr/psr-7/#3-2-1-psr-http-message-serverrequestinterface
 */
class Request extends Message implements ServerRequestInterface
{
    /**
     * HTTP method.
     * @var string
     */
    protected $method;

    /**
     * Array of allowed HTTP methods.
     * @var array
     */
    protected $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];

    /**
     * URI object.
     * @var \Psr\Http\Psr\Http\Message\UriInterface
     */
    protected $uri;

    /**
     * Target of the request.
     * @var string
     */
    protected $requestTarget;

    /**
     * Collection of server parameters.
     * Generally comes from $_SERVER global variable.
     * @var \Students\Interfaces\CollectionInterface
     */
    protected $serverParams;

    /**
     * Collection of request cookies.
     * Generally comes from $_COOKIES global variable.
     * @var \Students\Interfaces\CollectionInterface
     */
    protected $cookies;

    /**
     * Collection of elements from query string.
     * Generally comes from $_GET global variable.
     * @var \Students\Interfaces\CollectionInterface
     */
    protected $queryParams;

    /**
     * Request body parsed into array or string (if possible).
     * @var array|object|null
     */
    protected $parsedBody = false;

    /**
     * Application specific attributes.
     * @var \Students\Interfaces\CollectionInterface
     */
    protected $attributes;

    /**
     * Constructor.
     *
     * @param HeadersCollectionInterface $headers HTTP Request headers.
     * @param StreamInterface $body Request body.
     * @param string $method Request method.
     * @param UriInterface $uri Request URI.
     * @param array $serverParams Server parameters.
     * @param array $cookies Cookies.
     */
    public function __construct(
        HeadersCollectionInterface $headers,
        StreamInterface $body,
        $method,
        UriInterface $uri,
        array $serverParams,
        array $cookies
    )
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->method = $method;
        $this->uri = $uri;
        $this->serverParams = new Collection($serverParams);
        $this->queryParams = StringUtils::parseQueryString($uri->getQuery());
        $this->cookies = new Collection($cookies);
        $this->attributes = new Collection([]);
    }

    /**
     * Creates new Request instance from server parameters.
     *
     * @param array $server Array with server parameters.
     * This usually comes from $_SERVER superglobal.
     *
     * @return Request Request created from server parameters.
     */
    public static function fromServer(array $server)
    {
        $headers = Headers::fromServer($server);
        $uri = Uri::fromServer($server);
        $body = new Stream(fopen('php://temp', 'w+'));
        $method = $server['REQUEST_METHOD'];
        $cookies = [];

        $request = new Request($headers, $body, $method, $uri, $server, $cookies);

        if($method === 'POST') {
            // Think about better way of doing this
            $request = $request->withParsedBody($_POST);
        }

        return $request;
    }

    /**
     * Retains Request immutability.
     *
     * This method does nothing.
     */
    public function __set($name, $value)
    {
        // Retain immutability.
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        $uriTarget = '/';
        if(!is_null($this->uri)) {
            $uriTarget = $this->uri->getPath();
        }
        return is_null($this->requestTarget) ? $uriTarget : $this->requestTarget;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        if(!in_array($method, $this->allowedMethods)) {
            throw new \InvalidArgumentException('Invalid HTTP method. It must be one of these: '
                . implode(', ', $this->allowedMethods));
        }
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        if($preserveHost === true) {
            // If the Host header is missing or empty, and the new URI contains a
            // host component
            if( (!$this->hasHeader('Host') || $this->getHeader('Host')[0] === '') && $uri->getHost() !== '') {
                $clone = $clone->withHeader('Host', $uri->getHost());
            }
        }
        $clone->uri = $uri;
        return $clone;
    }

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams->all();
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookies->all();
    }

    /**
     * Return an instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->cookies = new Collection($cookies);
        return $clone;
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams->all();
    }

    /**
     * Retrieve query parameter by it's name. Returns a specified default value
     * if parameter with a given name does not exist.
     *
     * This method is not a part of PSR-7 specification.
     *
     * @param string $name Name of the parameter to retrieve.
     * @param mixed $default Value to return if parameter does not exist.
     *
     * @return mixed Query parameter with a given name, or a default value if
     * parameter does not exist.
     */
    public function getQueryParam($name, $default = null)
    {
        return $this->queryParams->get($name, $default);
    }

    /**
     * Filters a query parameter with a given name. If parameter matches the element
     * in the $filter array, returns the element. If parameter does not exist or
     * it's value does not match anything form $filter array, returns a default value.
     *
     * This method is not a part of PSR-7 specification.
     *
     * @param string $name Name of the parameter to filter.
     * @param array  $filter Array with correct values for filtering.
     * @param mixed $default A value to return if filtering failed.
     *
     * @return mixed Query parameter or a default value.
     */
    public function filterQueryParam($name, array $filter, $default = null)
    {
        $parameter = $this->queryParams->get($name, $default);
        return (in_array($parameter, $filter)) ? $parameter : $default;
    }

    /**
     * Return an instance with the specified query string arguments.
     *
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     *
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = new Collection($query);
        return $clone;
    }

    /**
     * Returns value of Conent-Type header for current request.
     * If Content-Type header is missing or empty returns null.
     * @return string|null Request content type.
     */
    public function getContentType()
    {
        $type = $this->getHeader('Content-Type');
        return empty($type) ? null : $type[0];
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        if(is_null($this->body)) {
            return null;
        }
        if($this->parsedBody !== false) {
            return $this->parsedBody;
        }

        $parsedData = null;
        // Parse body
        switch($this->getContentType()) {
            case 'application/x-www-form-urlencoded':
                $parsedData = parse_str((string)$this->getBody());
                break;
            case 'application/json':
                $parsedData = json_decode((string)$this->getBody(), true);
                break;
        }
        $this->parsedBody = $parsedData;
        return $parsedData;
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return static
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data)
    {
        if(!is_null($data) && !is_object($data) && !is_array($data)) {
            throw new \InvalidArgumentException('Body parameters must be an array, object or null.');
        }
        $clone = clone $this;
        $clone->parsedBody = $data;
        return $clone;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes->all();
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $default Default value to return if the attribute does not exist.
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes->get($name, $default);
    }

    /**
     * Returns an instance with the specified derived request attributes.
     *
     * @param array $attributes Attributes to add.
     *
     * @return static
     */
    public function withAttributes(array $attributes)
    {
        $clone = clone $this;
        foreach($attributes as $key => $value) {
            $clone->attributes->set($key, $value);
        }
        return $clone;
    }

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes->set($name, $value);
        return $clone;
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @return static
     */
    public function withoutAttribute($name)
    {
        $clone = clone $this;
        $clone->attributes->remove($name);
        return $clone;
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * @return static
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        throw new NotImplementedException(__METHOD__);
    }
}