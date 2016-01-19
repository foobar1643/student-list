<?php

class ViewFiller {

  private $view_settings;

  public function __construct() {
    $this->view_settings = new stdClass;
    $this->view_settings->error = NULL;
    $this->view_settings->success = NULL;
    $this->view_settings->page_data = NULL;
  }

  public function set_display_settings($display_settings) {
    $this->view_settings = $display_settings;
  }

  public function show_page() {
    $this->view_settings->page_title = "Заполнитель базы данных";
    $this->view_settings->nav_title = "filler";

    include_once("design/template/header.html");
    include_once("design/template/filler.html");
  }
}
