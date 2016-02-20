<?php

namespace App\Controller;
use \App\Model\Student;
use \App\Helper\TokenHelper;
use \App\Helper\FormHelper;
use \App\Container;

class ControllerForm {

    protected $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $viewSettings = array();
        $dataGateway = $this->container->getDataGateway();
        $formValidator = new FormHelper();
        $tokenHelper = new TokenHelper();
        if($_POST) {
            if(!$tokenHelper->checkCsrfToken($_POST['csrf_field'], $_COOKIE['token'])) { //!isset($_POST['csrf_field']) && !isset($_COOKIE['token']) && $_POST['csrf_field'] != $_COOKIE['token']
                $viewSettings['error'] = true;
            } else {
                $student = new Student();
                $fields = $formValidator->getAllowedFields();
                foreach($fields as $key) {
                    $student->$key = isset($_POST[$key . '_field']) ? strval($_POST[$key . '_field']) : '';
                }
                $errors = $formValidator->validateStudent($student);
                if(!isset($_COOKIE['auth']) && $dataGateway->checkEmail($student->email) != 0) {
                    $errors['email_exists'] = true;
                }
                if(!$errors) { // If there is no errors in form
                    if(isset($_COOKIE['auth'])) { // if user has an auth cookie
                        $auth = $dataGateway->selectToken($_COOKIE['auth']); // select a record from the database with user auth token
                        if(isset($auth->id) && $auth->id == $student->id) { // if there is an id for this token and it's equal to id in form
                            $dataGateway->update_student($student);
                            header("Location: form.php?notify=success");
                            die();
                        } else {
                            $viewSettings['error'] = true;
                        }
                    } else { // if user doesn't have an auth cookie
                        $student->token = $tokenHelper->generateToken(); // generate a new token for user
                        $student_id = $dataGateway->add_student($student); // add new student to the database and get his id
                        setcookie("auth", $student->token, time()+36000000); // send cookie with auth token to user
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
            if(isset($auth->id)) {
                $currentStudent = $dataGateway->fetch_student($auth->id);
                $viewSettings['student'] = $currentStudent;
            }
        }
        $userToken = $_COOKIE['token'];
        if(!isset($_COOKIE['token'])) {
            $userToken = $tokenHelper->generateToken();
        }
        $tokenHelper->setCsrfToken($userToken);
        $viewSettings["csrf_token"]  = $userToken;
        $viewSettings["pageTitle"]  = "Добавить запись";
        $viewSettings["navTitle"]  = "form";
        include("../templates/form.html");
    }
}