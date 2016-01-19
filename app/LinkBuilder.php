<?php

class LinkBuilder {

  public $filename;
  public $is_paging;
  public $page_number;
  public $is_searching;
  public $search_pattern;
  public $is_sorting;
  public $sort_key;
  public $sort_type;

  public function __construct() {

  }

  public function get_link_for_sorting($sort_key, $sort_type) {
    $link = $this->filename . "?sort=" . $sort_key . "&type=" . $sort_type;
    if($this->is_paging) {
      $link .= "&page=" . $this->page_number;
    }
    return $link;
  }

  public function get_link_for_page($page_number) {
    $link = $this->filename . "?page=" . $page_number;
    if($this->is_sorting) {
      $link .= "&sort=" . $this->sort_key;
      $link .= "&type=" . $this->sort_type;
    }
    if($this->is_searching) {
      $link .= "&search=" . $this->search_pattern;
    }
    return $link;
  }
}
