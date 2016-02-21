<?php

namespace App\Helper;

class PaginationHelper {

    private $records;
    private $recordsPerPage;
    private $totalPages;

    public function __construct($totalRecords, $recordsPerPage) {
        $this->records = $totalRecords;
        $this->recordsPerPage = $recordsPerPage;
        $this->totalPages = $this->countPages();
    }

    public function getPages() {
        return $this->totalPages;
    }

    public function getOffset($page) {
        $offset = null;
        for($i = 1; $i < $page; $i++) $offset += $this->recordsPerPage;
        return $offset;
    }

    public function checkPage($page) {
        if($page > $this->totalPages) return 1;
        else return $page;
    }

    private function countPages() {
        $pages = null;
        $page = $this->records;
        while(0 < $page) {
            $page -= $this->recordsPerPage;
            $pages++;
        }
        return $pages;
    }
}