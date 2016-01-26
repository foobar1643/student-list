<?php

namespace App\Controller;
use \App\Model\Student;
use \App\Database\StudentDataGateway;
use \App\ExceptionHandler;
use \App\Bootstrap as Bootstrap;

class ControllerFiller {

  private $names;
  private $surnames;
  private $data;
  private $student;
  private $student_gateway;
  private $group_source;

  public function __construct() {
    $this->data = array("names" => array(), "surnames" => array());
    $this->group_source = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    $this->student_gateway = new StudentDataGateway(Bootstrap::getPDO());
  }

  private function read_names() {
    $this->names = fopen("../data/names.txt", "r");
    $this->surnames = fopen("../data/surnames.txt", "r");
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
      if(rand(0,1) == 0) $this->student->gender = "male";
      else $this->student->gender = "female";
      for($x = 0; $x < rand(3, 4); $x++) $this->student->sgroup .= $this->group_source[rand(0, count($this->group_source))];
      $this->student->email = "datafiller@gmail.com";
      $this->student->byear = "19" . rand(1,9) . rand(0,9);
      $this->student->status = "resident";
      $this->student->rating = rand(1,100);
      $this->student_gateway->add_student($this->student);
    }
  }

  public function run() {
      if($_POST) {
          if($_POST['count_field'] < 100 && $_POST['count_field'] > 0) {
              $this->fill_database($_POST['count_field']);
              $viewSettings['success'] = $_POST['count_field'];
          } else {
              $viewSettings['error'] = true;
          }
      }
      $viewSettings['pageTitle'] = "Заполнитель базы данных";
      $viewSettings['navTitle'] = "filler";
      include_once("../templates/header.html");
      include_once("../templates/filler.html");
  }
}
