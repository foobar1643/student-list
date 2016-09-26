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

use Students\Utility\Collection;
use Students\Interfaces\Http\HeadersCollectionInterface;

/**
 * A collection to store HTTP headers with case-insensitive names.
 *
 * @link http://www.php-fig.org/psr/psr-7/#1-2-http-headers
 */
class Headers extends Collection implements HeadersCollectionInterface
{
    /**
     * Constructor.
     *
     * @param array $data Data to store in the collection.
     */
    public function __construct(array $data)
    {
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Creates headers collection from array.
     *
     * @param array $server [description]
     *
     * @return static
     */
    public static function fromServer(array $server)
    {
        $headers = new Headers([]);
        foreach($server as $key => $value) {
            if(substr($key, 0, 4) === 'HTTP') {
                // Strip the HTTP_ from the beginning of the string.
                $normalizedKey = strtolower(substr_replace($key, '', 0, 5));
                // Replace every underscore with hyphen.
                $normalizedKey = str_replace('_', '-', $normalizedKey);
                // Normalize key case.
                $normalizedKey = ucwords(strtolower($normalizedKey), '-');
                // Add a header to the collection
                $headers->set($normalizedKey, $value);
            }
        }
        return $headers;
    }

    /**
     * Retrieves all HTTP headers in form of associative array.
     *
     * @return array An associative array of HTTP headers.
     */
    public function all()
    {
        $headers = parent::all();
        $normalized = [];
        foreach($headers as $key => $storage) {
            $normalized[$storage['originalName']] = $storage['values'];
        }
        return $normalized;
    }

    /**
     * Appends given value to an existing header in the collection.
     *
     * @param string $name Name of an element in the collection.
     * @param mixed $value Value to append.
     *
     * @return void
     */
    public function add($name, $value)
    {
        if(!$this->has($name)) {
            $this->set($name, $value);
            return;
        }
        array_push($this->data[$this->normalizeName($name)]['values'], $value);
    }

    /**
     * Adds a header with a given case-insensitive name to the collection.
     *
     * Warning: This method will overwrite an existing item with new values.
     * If you want to add a value to an existing item, use add($name, $value).
     *
     * @param string $name Name of an element in the collection.
     * @param mixed $value Value to add.
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->data[$this->normalizeName($name)]['originalName'] = $name;
        $this->data[$this->normalizeName($name)]['values'] = is_array($value) ? $value : [$value];
    }

    /**
     * Returns an array that represents an HTTP header. With array key being a header name
     * and key's value being header value respectively. Returns default value
     * if a header with given name is not present in the collection.
     *
     * Original case in header name is preserved.
     *
     * @param string $name Name of an element in the collection.
     * @param mixed $default Value to return if header is not in collection.
     *
     * @return array
     */
    public function get($name, $default = null)
    {
        $data = parent::get($this->normalizeName($name), $default);
        return ($data !== $default) ? $data['values'] : $data;
    }

    /**
     * Returns true if collection has header with given case-insensitive name.
     * Returns false otherwise.
     *
     * @param string $key A key to check.
     *
     * @return bool
     */
    public function has($name)
    {
        return parent::has($this->normalizeName($name));
    }

    /**
     * Removes a header with a given case-insensitive name from the collection.
     *
     * @param string $name Header name to remove.
     *
     * @return void
     */
    public function remove($name)
    {
        parent::remove($this->normalizeName($name));
    }

    /**
     * Returns normalized header name for case-insensitive storage.
     *
     * @param string $name Name to normalize.
     *
     * @return string Normalized header name.
     */
    protected function normalizeName($name)
    {
        return ucfirst(strtolower($name));
    }
}