<?php

namespace App\Controller;

use Pimple\Container;
use \App\Model\Student;
use \App\Helper\TokenHelper;
use \App\Helper\RegistrationHelper;

class ControllerForm {

    private $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $pageTitle = "Добавить запись";
        $navTitle = "form";
        $student = new Student();
        $dataGateway = $this->container["dataGateway"];
        $regHelper = new RegistrationHelper($dataGateway);
        $tokenHelper = new TokenHelper();
        $csrfToken = $tokenHelper->setCsrfToken();
        $isAuthorized = false;
        if($tokenHelper->getAuthToken() != null) {
            $check = $dataGateway->selectStudent($tokenHelper->getAuthToken());
            if($check->id != null) {
                $student = $check;
                $isAuthorized = true;
                $pageTitle  = "Редактировать запись";
            }
        }
        if($_SERVER['REQUEST_METHOD'] == "POST" && $tokenHelper->checkCsrfToken($_POST['csrf_field'])) {
            foreach($regHelper->getAllowedFields() as $key) {
                $student->$key = isset($_POST[$key . '_field']) ? strval($_POST[$key . '_field']) : '';
            }
            $errors = $regHelper->validateStudent($student);
            if(!$errors) {
                $student->token = $tokenHelper->setAuthToken();
                if($isAuthorized && $student->id != null) { // updating
                    $oldStudent = $dataGateway->selectStudent($tokenHelper->getAuthToken());
                    foreach($regHelper->getAllowedFields() as $key) {
                        if($student->$key != null) {
                            $oldStudent->$key = $student->$key;
                        }
                    }
                    $dataGateway->updateStudent($oldStudent);
                } else { // creating
                    $dataGateway->addStudent($student);
                }
                header("Location: form.php?notify=success");
                die();
            }
        }
        if($_GET['notify'] == 'success' && !isset($errors)) {
            $success = true;
        }
        include("../templates/form.html");
    }
}