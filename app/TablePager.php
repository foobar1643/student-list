<?php

class TablePager {

  private $records;
  private $records_per_page;
  private $total_pages;
  private $page_link;

  public function __construct($total_records, $records_per_page, $page_link) {
    $this->records = $total_records;
    $this->records_per_page = $records_per_page;
    $this->page_link = $page_link;
  }

  public function count_total_pages() {
    $page = $this->records;
    $this->total_pages = null;
    while(0 < $page) {
      $page = $page - $this->records_per_page;
      $this->total_pages++;
    }
  }

  public function get_total_pages() {
    $this->count_total_pages();
    return $this->total_pages;
  }

  public function get_offset_for_page($page) {
    for($i = 1; $i < $page; $i++) $offset = $offset + 15;
    return $offset;
  }

  public function get_link_to_page($page) {
    $this->count_total_pages();
    if($page <= 1) $page = 1;
    if($page >= $this->total_pages) $page = $this->total_pages;
    return $page_link . $page;
  }
}
