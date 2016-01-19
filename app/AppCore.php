<?php

class AppCore {

  protected $page;
  protected $connection_error;
  protected $view_settings;
  protected $pdo;

  public function __construct() {
    $this->view_settings = new stdClass();
    $this->view_settings->page_data = new stdClass();
    $this->view_settings->error = array();
    $this->view_settings->page_data->student = array();

    $dsn = AppConfig::db_type . ":dbname=" . AppConfig::db_name . ";host=" . AppConfig::db_host; //"pgsql:dbname=students;host=127.0.0.1"
    try {
      $this->pdo = new PDO($dsn, AppConfig::db_user, AppConfig::db_pswd);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      print("Database connection error. " . $e->getMessage());
      exit();
    }
  }

}
