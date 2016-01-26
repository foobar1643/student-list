<?php

namespace App\Controller;
use \App\Model\Student;
use \App\Database\StudentDataGateway;
use \App\Model\Helper\TokenHelper;
use \App\Model\Helper\FormHelper;
use \App\Bootstrap as Bootstrap;

class ControllerForm {

    public function run() {
        $viewSettings = array();
        $dataGateway = new StudentDataGateway(Bootstrap::getPDO());
        $formValidator = new FormHelper();
        if($_POST) {
            if(!isset($_POST['csrf_field']) && !isset($_COOKIE['token']) && $_POST['csrf_field'] != $_COOKIE['token']) {
                $viewSettings['error'] = true;
            } else {
                $errors = $formValidator->validateForm($_POST);
                $student = new Student();
                foreach(array_keys(get_object_vars($student)) as $key) {
                    $student->$key = isset($_POST[$key . '_field']) ? strval($_POST[$key . '_field']) : '';
                }
                if(!$errors) { // If there is no errors in form
                    if(isset($_COOKIE['auth'])) { // if user has an auth cookie
                        $auth = $dataGateway->selectToken($_COOKIE['auth']); // select a record from the database with user auth token
                        if(isset($auth['id']) && $auth['id'] == $student->id) { // if there is an id for this token and it's equal to id in form
                            $dataGateway->update_student($student);
                            header("Location: form.php?notify=success");
                            die();
                        } else {
                            $viewSettings['error'] = true;
                        }
                    } else { // if user doesn't have an auth cookie
                        $student->token = TokenHelper::generate_token(); // generate a new token for user
                        $student_id = $dataGateway->add_student($student); // add new student to the database and get his id
                        setcookie("auth", $student->token, time()+36000000); // send cookie with token to user
                        header("Location: form.php?notify=success");
                        die();
                    }
                } else { // If there is errors in form
                    $viewSettings['error'] = $errors;
                    $viewSettings['student'] = $student;
                }
            }
        }
        if(isset($_GET['notify']) && !isset($viewSettings['error'])) {
            $viewSettings['success'] = true;
        }
        if(isset($_COOKIE['auth']) && !isset($viewSettings['error'])) {
            $auth = $dataGateway->selectToken($_COOKIE['auth']);
            if(isset($auth['id'])) {
                $currentStudent = $dataGateway->fetch_student($auth['id']);
                $viewSettings['student'] = $currentStudent;
            }
        }
        $userToken = $_COOKIE['token'];
        if(!isset($_COOKIE['token'])) {
            $userToken = TokenHelper::generate_token();
        }
        setcookie("token", $userToken, time()+36000000);
        $viewSettings["csrf_token"]  = $userToken;
        $viewSettings["pageTitle"]  = "Добавить запись";
        $viewSettings["navTitle"]  = "form";
        include_once("../templates/header.html");
        include_once("../templates/form.html");
    }
}