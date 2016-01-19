<?php

class ViewIndex {

  private $view_settings;
  public $link_builder;

  public function __construct() {
    $this->view_settings = new stdClass;
    $this->link_builder = new LinkBuilder();
    $this->link_builder->filename = "index.php";
    $this->view_settings->error = NULL;
    $this->view_settings->success = NULL;
    $this->view_settings->page_data = NULL;
  }

  public function set_display_settings($display_settings) {
    $this->view_settings = $display_settings;
    $this->link_builder->sort_type = $this->view_settings->page_data->sort_type;
    $this->link_builder->sort_key = $this->view_settings->page_data->sort_key;
    if($this->view_settings->page_data->sort_type == "desc") $this->view_settings->page_data->fl_sorting_type = "asc";
    else $this->view_settings->page_data->fl_sorting_type = "desc";
  }

  public function show_page() {
    $this->view_settings->page_title = "Главная страница";
    $this->view_settings->nav_title = "index";

    include_once("design/template/header.html");
    include_once("design/template/index.html");
  }
}
