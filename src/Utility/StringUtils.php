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
        $result = null;
        $source = str_split('abcdefghijklmnopqrstuvwxyz'
          .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
          .'0123456789');
        for($i = 0; $i < $length; $i++) {
            $result .= $source[mt_rand(0, count($source) - 1)];
        }
        return $result;
    }

    /**
     * Parses query string into a collection of parameters.
     *
     * @todo Think about better way of doing this
     *
     * @param string $queryString Query string to parse.
     *
     * @return \Students\Utility\Collection Collection of parsed data.
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