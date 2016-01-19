<?php

class ControllerForm extends AppCore {

  private $student;
  private $student_gateway;
  private $form_validator;
  private $database;

  public function __construct() {
    $this->student = new Student();
    $this->page = new ViewForm();
    $this->form_validator = new FormValidator();
    parent::__construct();
    $this->student_gateway = new StudentDataGateway($this->pdo);
  }

  public function process_get_request($get_data) {
    if($get_data['notify'] == "success") {
      $this->view_settings->success = true;
    }
  }

  public function process_post_request($post_data) {
    if(isset($post_data["token_field"]) && isset($_COOKIE["token"]) && $post_data["token_field"] != $_COOKIE["token"]) {
      $this->view_settings->error = true;
    } else {
      $this->student->id = $this->form_validator->process_field($post_data["id_field"]);
      $this->student->name = $this->form_validator->process_field($post_data["name_field"]);
      $this->student->surname = $this->form_validator->process_field($post_data["surname_field"]);
      $this->student->gender = $this->form_validator->process_field($post_data["gender_field"]);
      $this->student->group = $this->form_validator->process_field($post_data["group_field"]);
      $this->student->email = $this->form_validator->process_field($post_data["email_field"]);
      $this->student->byear = $this->form_validator->process_field($post_data["byear_field"]);
      $this->student->status = $this->form_validator->process_field($post_data["status_field"]);
      $this->student->rating = $this->form_validator->process_field($post_data["rating_field"]);
      $error = $this->form_validator->validate_student($this->student);
      if(!$error) { // if there is no errors
        if($_COOKIE['auth']) { // if user is authorized
          $auth = $this->form_validator->select_token($_COOKIE['auth'], $this->pdo); // select a record in database with user token
          if($auth['student_id'] && $auth['student_id'] == $this->student->id) { // if there is a user id for this token and it's equeal to id in form
            $this->student_gateway->update_student($this->student); // update a database record for this student
            header("Location: form.php?notify=success"); // show success message to user
          } else { // if there is no user id for this token or his id mismatches the form id
            $this->view_settings->error = true; // set an error variable to true
          }
        } else { // if user is not authorized
          $student_id = $this->student_gateway->add_student($this->student); // add new student to the database and get his id
          $auth_token = $this->form_validator->generate_token(); // generate a new auth token
          $this->form_validator->store_token($auth_token, $student_id, $this->pdo); // store user token and id in the database
          setcookie("auth", $auth_token, time()+36000000); // send cookie with token to user
          header("Location: form.php?notify=success"); // show success message to user
        }
      } else { // if there is errrors
        $this->view_settings->error = $error;
        $this->view_settings->page_data->student = $this->student;
      }
    }
  }

  public function run() {
    if($_COOKIE["token"]) {
      $user_token = $_COOKIE["token"];
      setcookie("token", $user_token, time()+360000);
    } else {
      $user_token = $this->form_validator->generate_token();
      setcookie("token", $user_token, time()+360000);
    }
    if($_COOKIE['auth']) {
      $auth = $this->form_validator->select_token($_COOKIE['auth'], $this->pdo);
      if($auth['student_id']) { // if there is a student id for this token
        $current_student = $this->student_gateway->fetch_student($auth['student_id']); // get full information about student based on his id
        $this->view_settings->page_data->user_id = $auth['student_id']; // pass student id to hidden form field
        $this->view_settings->page_data->student = $current_student; // pass student full information to form fields
      }
    }
    $this->view_settings->page_data->csrf_token = $user_token;
    $this->page->set_display_settings($this->view_settings);
    $this->page->show_page();
  }
}
