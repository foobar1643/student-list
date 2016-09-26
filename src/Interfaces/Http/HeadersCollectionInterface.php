<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Interfaces\Http
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Interfaces\Http;

use Students\Interfaces\CollectionInterface;

/**
 * Describes a collection that stores HTTP headers with case-insensitive names.
 *
 * Extends CollectionInterface.
 */
interface HeadersCollectionInterface extends CollectionInterface
{
    /**
     * Appends given value to an existing item in the collection.
     */
    public function add($name, $value);
}