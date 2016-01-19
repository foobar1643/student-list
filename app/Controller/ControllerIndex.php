<?php

class ControllerIndex extends AppCore {

  private $student_gateway;
  private $student_count;
  private $pager;
  private $total_pages;
  private $offset;
  private $sorting_pattern;
  private $sorting_patterns;
  private $searching_pattern;

  public function __construct() {
    $this->page = new ViewIndex();
    parent::__construct();
    $this->offset = null;
    $this->sorting_patterns = array(1 => "name", 2 => "surname", 3 => "sgroup", 4 => "rating");
    $this->sorting_pattern = $this->sorting_patterns[4];
    $this->sorting_type = "desc";
    $this->searching_pattern = null;
    $this->student_gateway = new StudentDataGateway($this->pdo);
    $this->student_count = $this->student_gateway->get_total_students(null);
    $this->pager = new TablePager($this->student_count["count"], AppConfig::elem_per_page, "index.php?page=");
    $this->total_pages = $this->pager->get_total_pages();
  }

  public function process_get_request($get_data) {
    if($get_data['page'] && $get_data['page'] <= $this->total_pages) {
      $this->offset = $this->pager->get_offset_for_page($get_data['page']);
      $this->view_settings->page_data->current_page = $get_data['page'];
      $this->page->link_builder->is_paging = true;
      $this->page->link_builder->page_number = $get_data['page'];
    }
    if($get_data['sort'] && $get_data['type']) {
      $index = array_search($get_data['sort'], $this->sorting_patterns);
      if($this->sorting_patterns[$index]) {
        $this->page->link_builder->is_sorting = true;
        $this->sorting_pattern = $this->sorting_patterns[$index];
        if($get_data['type'] == "asc") $this->sorting_type = "asc";
      }
    }
    if($get_data['search']) {
      $this->page->link_builder->is_searching = true;
      $this->page->link_builder->search_pattern = $get_data['search'];
      $this->searching_pattern = $get_data['search'];
      $this->student_count = $this->student_gateway->get_total_students($this->searching_pattern);
      $this->pager = new TablePager($this->student_count["count"], AppConfig::elem_per_page, "index.php?page=");
      $this->total_pages = $this->pager->get_total_pages();
    }
  }

  public function run() {
    $student = $this->student_gateway->select_students($this->offset, $this->sorting_pattern, $this->sorting_type, $this->searching_pattern);
    if(!$this->view_settings->page_data->current_page) $this->view_settings->page_data->current_page = 1;
    $this->view_settings->page_data->next_page = $this->pager->get_link_to_page($this->view_settings->page_data->current_page + 1);
    $this->view_settings->page_data->previous_page = $this->pager->get_link_to_page($this->view_settings->page_data->current_page - 1);
    $this->view_settings->page_data->total_pages = $this->total_pages;
    $this->view_settings->page_data->sort_key = $this->sorting_pattern;
    $this->view_settings->page_data->sort_type = $this->sorting_type;
    $this->view_settings->page_data->student = $student;
    $this->page->set_display_settings($this->view_settings);
    $this->page->show_page();
  }
}
