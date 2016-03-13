<?php

namespace App\Controller;

use Pimple\Container;
use \App\Model\Student;

class ControllerForm implements AppController {

    private $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $pageTitle = "Добавить запись";
        $navTitle = "form";
        $student = new Student();
        $errors = null;
        $dataGateway = $this->container["dataGateway"];
        $regHelper = $this->container["registrationHelper"];
        $csrfHelper = $this->container["csrfHelper"];
        $authHelper = $this->container["authHelper"];
        $csrfToken = $csrfHelper->setCsrfToken();
        $auth = $authHelper->getAuthorizedStudent();
        if($auth != false) {
            $student = $auth;
            $pageTitle  = "Редактировать запись";
        }
        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['csrf_field']) && $csrfHelper->checkCsrfToken($_POST['csrf_field'])) {
            $student = $regHelper->getFormData($student);
            $errors = $regHelper->validateStudent($student);
            if(!$errors) {
                if($auth != false) { // authorized
                    $auth = $regHelper->getUpdatedFields($auth, $student);
                    $dataGateway->updateStudent($auth);
                } else { // not authorized
                    $student->token = $authHelper->authorizeStudent();
                    $dataGateway->addStudent($student);
                }
                header("Location: index.php?notify=success");
                die();
            }
        }
        include("../templates/form.html");
    }
}