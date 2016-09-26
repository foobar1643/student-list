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

/**
 * Exception thrown if a method is not implemented.
 *
 * Extends BadMethodCallException
 */
class NotImplementedException extends \BadMethodCallException
{
    /**
     * Exception constructor.
     * 
     * @param string $method Not implemented method.
     * Typically this is __METHOD__ magic constant.
     */
    public function __construct($method)
    {
        parent::__construct("Method {$method} is not implemented.");
    }
}