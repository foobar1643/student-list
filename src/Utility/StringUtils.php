<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Utility
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Utility;

/**
 * Utility class that works with strings.
 */
class StringUtils
{
    /**
     * Generates random string with a given length.
     *
     * @param int $length Length of the generated string.
     *
     * @return string Randomly generated string.
     */
    public static function generate($length)
    {

    }

    /**
     * Parses query string into a collection of parameters.
     *
     * @param  [type] $queryString [description]
     *
     * @return [type]              [description]
     */
    public static function parseQueryString($queryString)
    {
        // Array with the results.
        $result = new Collection([]);
        // Explode the query string by '&' symbol.
        $keyValue = !empty($queryString) ? explode('&', $queryString) : [];
        foreach($keyValue as $key => $value) {
            $param = explode('=', $value);
            $result->set($param[0], $param[1]);
        }
        return $result;
    }
}