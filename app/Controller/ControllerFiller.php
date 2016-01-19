<?php

class ControllerFiller extends AppCore {

  private $names;
  private $surnames;
  private $data;
  private $student;
  private $student_gateway;
  private $group_source;

  public function __construct() {
    $this->page = new ViewFiller();
    $this->data = array("names" => array(), "surnames" => array());
    $this->group_source = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    parent::__construct();
    $this->student_gateway = new StudentDataGateway($this->pdo);
  }

  private function read_names() {
    $this->names = fopen("../names.txt", "r");
    $this->surnames = fopen("../surnames.txt", "r");
    if($this->names && $this->surnames) {
      while(($line = fgets($this->names)) !== false) array_push($this->data["names"], $line);
      while(($line = fgets($this->surnames)) !== false) array_push($this->data["surnames"], $line);
    } else {
      print("File opening error.");
      exit();
    }
  }

  public function fill_database($count) {
    $this->read_names();
    for($i = 0; $i < $count; $i++) {
      $this->student = new Student();
      $this->student->name = $this->data["names"][rand(0, count($this->data["names"])-1)];
      $this->student->surname = $this->data["surnames"][rand(0, count($this->data["surnames"])-1)];
      if(rand(0,1) == 0) $this->student->gender = "m";
      else $this->student->gender = "f";
      for($x = 0; $x < rand(3, 4); $x++) $this->student->group .= $this->group_source[rand(0, count($this->group_source))];
      $this->student->email = "datafiller@gmail.com";
      $this->student->byear = "19" . rand(0,9) . rand(0,9);
      $this->student->status = "mov";
      $this->student->rating = rand(0,100);
      $this->student_gateway->add_student($this->student);
    }
  }

  public function process_post_request($post_data) {
    $this->fill_database($post_data['count_field']);
    $this->view_settings->success = $post_data['count_field'];
  }

  public function run() {
    $this->page->set_display_settings($this->view_settings);
    $this->page->show_page();
  }
}
