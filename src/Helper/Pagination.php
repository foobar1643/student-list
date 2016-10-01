<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Helper
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Helper;

/**
 * Helps the view create pagination.
 *
 * @todo Advanced pagination (like displaying only previous and two next pages).
 */
class Pagination
{
    /**
     * Total number of records in the database.
     * @var int
     */
    private $records;

    /**
     * Number of records to display per page.
     * @var int
     */
    private $recordsPerPage;

    /**
     * Total number of pages.
     * @var int
     */
    private $totalPages;

    /**
     * Constructor.
     *
     * @param int $totalRecords Total number of records in the database.
     * @param int $recordsPerPage A number of records to display per page.
     */
    public function __construct($totalRecords, $recordsPerPage)
    {
        $this->records = $totalRecords;
        $this->recordsPerPage = $recordsPerPage;
        $this->totalPages = $this->countPages();
    }

    /**
     * Returns total number of pages available.
     *
     * @return int Number of pages available.
     */
    public function getPages()
    {
        return $this->totalPages;
    }

    /**
     * Returns a database offset for a given page.
     *
     * @param int $page Page number.
     *
     * @return int Offset valud for database selections.
     */
    public function getOffset($page)
    {
        return ($page - 1) * $this->recordsPerPage;
    }

    /**
     * Returns a database limit value.
     *
     * @return int Limit value for database selections.
     */
    public function getLimit()
    {
        return $this->recordsPerPage;
    }

    /**
     * If page number is valid, returns it. If a page number is invalid,
     * returns '1'.
     *
     * @param int $page Page number to validate.
     *
     * @return int Given page number if it is valid, 1 otherwise.
     */
    public function validatePageNumber($page)
    {
        return ($page > $this->totalPages) ? 1 : $page;
    }

    /**
     * Calculates total pages using total records and records per page values.
     *
     * @return int Total number of pages available.
     */
    private function countPages()
    {
        return ceil($this->records / $this->recordsPerPage);
    }
}