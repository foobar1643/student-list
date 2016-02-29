<?php

namespace App\Controller;

use Pimple\Container;
use \App\Model\Student;
use \App\Helper\TokenHelper;
use \App\Helper\FormHelper;

class ControllerForm {

    private $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $viewSettings = array();
        $pageTitle  = "Добавить запись";
        $dataGateway = $this->container["dataGateway"];
        $formHelper = new FormHelper();
        $tokenHelper = new TokenHelper();
        if($_POST) {
            if(!$tokenHelper->checkCsrfToken($_POST['csrf_field'], $_COOKIE['token'])) { //!isset($_POST['csrf_field']) && !isset($_COOKIE['token']) && $_POST['csrf_field'] != $_COOKIE['token']
                $error = true;
            } else {
                $student = new Student();
                foreach($formHelper->getAllowedFields() as $key) {
                    $student->$key = isset($_POST[$key . '_field']) ? strval($_POST[$key . '_field']) : '';
                }
                if(!isset($_COOKIE['auth'])) {
                    $student->id = 0;
                }
                $errors = $formHelper->validateStudent($student);
                if($dataGateway->checkEmail($student->email, $student->id) != 0) {
                    $errors['email'] = "Такой e-mail уже занят другим пользователем.";
                }
                if(!$errors) { // If there is no errors in form
                    if(isset($_COOKIE['auth'])) { // if user has an auth cookie
                        $auth = $dataGateway->selectToken($_COOKIE['auth']); // select a record from the database with user auth token
                        if(isset($auth->id) && $auth->id == $student->id) { // if there is an id for this token and it's equal to id in form
                            $dataGateway->update_student($student);
                            header("Location: form.php?notify=success");
                            die();
                        } else {
                            $error = true;
                        }
                    } else { // if user doesn't have an auth cookie
                        $student->token = $tokenHelper->generateToken(); // generate a new token for user
                        $student_id = $dataGateway->add_student($student); // add new student to the database and get his id
                        setcookie("auth", $student->token, time()+36000000); // send cookie with auth token to user
                        header("Location: form.php?notify=success");
                        die();
                    }
                } else { // If there is errors in form
                    $error = $errors;
                }
            }
        }
        if(isset($_GET['notify']) && !isset($error)) {
            $success = true;
        }
        if(isset($_COOKIE['auth']) && !isset($error)) {
            $auth = $dataGateway->selectToken($_COOKIE['auth']);
            if(isset($auth->id)) {
                $currentStudent = $dataGateway->fetch_student($auth->id);
                $student = $currentStudent;
                $pageTitle  = "Редактировать запись";
            }
        }
        $userToken = $_COOKIE['token'];
        if(!isset($_COOKIE['token'])) {
            $userToken = $tokenHelper->generateToken();
        }
        $tokenHelper->setCsrfToken($userToken);
        $csrfToken = $userToken;
        $navTitle = "form";
        include("../templates/form.html");
    }
}