<?php

namespace App\Helper;

/**
 * Counts total pages for a query, calculates a database offset for a given page.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 */
class PaginationHelper
{
    /** @var int $records Total number of records in the query. */
    private $records;
    /** @var int $recordsPerPage Number of records to display per page. */
    private $recordsPerPage;
    /** @var int $totalPages Total pages in the query. */
    private $totalPages;

    /**
     * Constructor.
     *
     * @param int $totalRecords Total number of records in the query.
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
     * @return int
     */
    public function getPages()
    {
        return $this->totalPages;
    }

    /**
     * Returns a database offset value for a given page.
     *
     * @param int $page Page number.
     *
     * @return int
     */
    public function getOffset($page)
    {
        return ($page - 1) * $this->recordsPerPage;
    }

    /**
     * Returns a database limit value.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->recordsPerPage;
    }

    /**
     * Validates a given page, if it's not valid - returns page number 1.
     *
     * @param int $page Page number to check.
     *
     * @return int
     */
    public function checkPage($page)
    {
        if($page > $this->totalPages) {
            return 1;
        }
        return $page;
    }

    /**
     * Calculates total pages using total records and records per page values.
     *
     * @return int
     */
    private function countPages()
    {
        return ceil($this->records / $this->recordsPerPage);
    }
}