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

/**
 * Value object representing the URI according to PSR-7.
 *
 * @todo Use getters instead of classfields?
 *
 * @link http://www.php-fig.org/psr/psr-7/#3-5-psr-http-message-uriinterface
 */
class Uri implements UriInterface
{
    /**
     * URI scheme.
     * @var string
     */
    protected $scheme = '';

    /**
     * Username for authority part.
     * @var string
     */
    protected $user = '';

    /**
     * Password for authority part.
     * @var string
     */
    protected $password = '';

    /**
     * URI host.
     * @var string
     */
    protected $host = '';

    /**
     * URI port.
     * @var int
     */
    protected $port = null;

    /**
     * URI path.
     * @var string
     */
    protected $path = '';

    /**
     * URI query.
     * @var string
     */
    protected $query = '';

    /**
     * URI fragment.
     * @var string
     */
    protected $fragment = '';

    /**
     * Array of schemes that is allowed in the URI.
     * @var string[]
     */
    protected $allowedSchemes = ['http', 'https'];


    /**
     * Constructor.
     *
     * @param string $scheme URI scheme.
     * @param string $host URI host.
     * @param int $port URI port.
     * @param string $path URI path.
     * @param string $query URI query.
     * @param string $fragment URI fragment.
     * @param string $user Username for authority part.
     * @param string $password Password for authority part.
     */
    public function __construct(
        $scheme,
        $host,
        $port = null,
        $path = '/',
        $query = '',
        $fragment = '',
        $user = '',
        $password = '')
    {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $this->filterHost($host);
        $this->port = $this->filterPort($port);
        $this->path = $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);
        $this->user = (empty($user)) ? '' : $user;
        $this->password = (empty($user) && empty($password)) ? '' : $password;
    }

    /**
     * Creates URI object from $_SERVER superglobal variable.
     *
     *
     *
     * @return static
     */
    public static function fromString($uri)
    {
        if($parsed = parse_url($uri) === false) {
            throw new \InvalidArgumentException('Failed to parse the URI.');
        }
        $this->host = isset($parsed['scheme']) ? $this->filterScheme($parsed['scheme']) : '';
        $this->host = isset($parsed['host']) ? $this->filterHost($parsed['host']) : '';
        $this->host = isset($parsed['port']) ? $this->filterPort($parsed['port']) : '';
        $this->host = isset($parsed['path']) ? $this->filterPath($parsed['path']) : '';
        $this->host = isset($parsed['query']) ? $this->filterQuery($parsed['query']) : '';
        $this->host = isset($parsed['fragment']) ? $this->filterQuery($parsed['fragment']) : '';
        $this->host = isset($parsed['user']) ? $parsed['user'] : '';
        $this->host = isset($parsed['pass']) ? $parsed['pass'] : '';
    }

    public static function fromServer(array $server)
    {
        $scheme = empty($server['HTTPS']) ? 'http' : 'https';
        // Attempt to get host from server name.
        $host = !empty($server['SERVER_NAME']) ? $server['SERVER_NAME'] : '';

        $port = null;

        if(!empty($server['HTTP_HOST']) &&
          preg_match('/^([a-zA-Z0-9]+)(:[0-9]+)$/u', $server['HTTP_HOST'], $matched)) {
              $host = $matched[1];
              $port = (int) substr($matched[2], 1);
        }

        $pathUri = 'http://example.com' . (!empty($server['REQUEST_URI']) ? $server['REQUEST_URI'] : '/');

        $path = parse_url($pathUri, PHP_URL_PATH);

        $query = !empty($server['QUERY_STRING']) ? $server['QUERY_STRING'] : parse_url($pathUri, PHP_URL_QUERY);

        $fragment = '';

        $username = !empty($server['PHP_AUTH_USER']) ? $server['PHP_AUTH_USER'] : '';

        $password = !empty($server['PHP_AUTH_PASS']) ? $server['PHP_AUTH_PASS'] : '';

        return new static($scheme, $host, $port, $path, $query, $fragment, $username, $password);
    }

    /**
     * Returns filtered scheme.
     *
     * @todo Print out allowed schemes in the exception.
     *
     * @param string $scheme URI scheme.
     *
     * @throws \InvalidArgumentException If scheme is not allowed.
     *
     * @return string Filtered URI scheme.
     */
    protected function filterScheme($scheme)
    {
        if(!in_array($scheme, $this->allowedSchemes, true)) {
            throw new \InvalidArgumentException('Given scheme is not allowed.');
        }
        return strtolower($scheme);
    }

    /**
     * Returns filtered host.
     *
     * @param string $host URI host.
     *
     * @throws \InvalidArgumentException If host is not a string.
     *
     * @return string Filtered URI host.
     */
    protected function filterHost($host)
    {
        if(!is_string($host)) {
            throw new \InvalidArgumentException('Host must be a string.');
        }
        return strtolower($host);
    }

    /**
     * Returns filtered port.
     *
     * @param string $version URI port.
     *
     * @throws \InvalidArgumentException If port is not an integer,
     * if port is outside the established TCP and UDP port ranges.
     *
     * @return int Filtered URI port.
     */
    protected function filterPort($port)
    {
        if(!is_int($port) && $port < 1 && $port > 65535) {
            throw new \InvalidArgumentException('Port must be an integer in range of 1 to 65535.');
        }
        return $port;
    }

    /**
     * Returns true if current port is default for current scheme.
     *  Otherwise, returns false.
     *
     * @return bool
     */
    protected function isPortDefault()
    {
        return ($this->scheme === 'http' && $this->port === 80) || ($this->scheme === 'https' && $this->port === 443);
    }

    /**
     * Validates given path and returns encoded path according to the RFC 3986.
     *  This function WILL NOT double-encode any characters.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @throws \InvalidArgumentException If path is not a string.
     *
     * @return string Path encoded according to RFC 3986.
     */
    protected function filterPath($path)
    {
        if(!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string.');
        }
        return preg_replace_callback(
            '/[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+/',
            function($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    /**
     * Validates given query and returns encoded query according to the RFC 3986.
     *  This function WILL NOT double-encode any characters.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return string Query encoded according to RFC 3986.
     */
    protected function filterQuery($query)
    {
        return preg_replace_callback(
            '/[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+/',
            function($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $userInformation = '';
        $hostInformation = '';
        if(!empty($this->user)) {
            $userInformation .= (empty($this->password)) ? "{$this->user}@" : "{$this->user}:{$this->password}@";
        }
        $hostInformation .= (empty($this->host)) ? '' : $this->host;
        $hostInformation .= (empty($this->port) && $this->isPortDefault()) ? '' : ":{$this->port}";
        return $userInformation . $hostInformation;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        $userInformation = '';
        if(!empty($this->user)) {
            $userInformation .= (empty($this->password)) ? "{$this->user}" : "{$this->user}:{$this->password}";
        }
        return $userInformation;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return (!empty($this->port) && !empty($this->scheme) && !$this->isPortDefault()) ? $this->port : null;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $clone = clone $this;
        $clone->scheme = $this->filterScheme($scheme);
        return $clone;
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = (empty($user)) ? null : $password;
        return $clone;
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $clone = clone $this;
        $clone->host = $this->filterHost($host);
        return $clone;
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $clone = clone $this;
        $clone->port = (is_null($port)) ? '' : $this->filterPort($port);
        return $clone;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $clone = clone $this;
        $clone->path = $this->filterPath($path);
        return $clone;
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        if(!is_string($query)) {
            throw new \InvalidArgumentException('Query must be a string.');
        }
        $clone = clone $this;
        $clone->query = $this->filterQuery($query);
        return $clone;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        $clone = clone $this;
        $clone->fragment = $this->filterQuery($fragment);
        return $clone;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        $string = (empty($this->scheme)) ? '' : "{$this->scheme}://";
        $string .= (empty($this->getAuthority())) ? '' : $this->getAuthority();

        // Path adjustments
        // If the path is rootless and the authority is present - prefix the path
        // with "/".
        if(substr($this->path, 0) !== '/' && !empty($this->getAuthority())) {
            $string .= "/" . $this->path;
        }
        // If the path is starting with more than one "/" and no authority is present,
        // the starting slashes is reduced to one.
        else if(preg_match('/^(\/{1})(\/{2,})/', $this->path) && empty($this->getAuthority())) {
            $string .= preg_replace('/^(\/{1})(\/{2,})/', '$1', $this->path);
        } else {
            $string .= $this->path;
        }

        $string .= (empty($this->query)) ? '' : "?{$this->query}";
        $string .= (empty($this->fragment)) ? '' : "#{$this->fragment}";

        return $string;
    }
}