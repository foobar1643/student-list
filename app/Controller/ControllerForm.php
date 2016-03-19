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
        $isAuthorized = $authHelper->isAuthorized();
        if($isAuthorized) {
            $student = $authHelper->getAuthorizedStudent();
            $pageTitle  = "Редактировать запись";
        }
        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['csrf_field']) && $csrfHelper->checkCsrfToken($_POST['csrf_field'])) {
            $student = $this->getFormData($student);
            $errors = $regHelper->validateStudent($student);
            if(!$errors) {
                if($isAuthorized) { // authorized
                    $dataGateway->updateStudent($student);
                } else { // not authorized
                    $student->token = $authHelper->createAuthToken();
                    $dataGateway->addStudent($student);
                    $authHelper->authorizeStudent($student);
                }
                header("Location: index.php?notify=success");
                return;
            }
        }
        include("../templates/form.html");
    }

    public function getFormData(Student $student) {
        foreach($this->container["registrationHelper"]->getAllowedFields() as $key) {
            if(isset($_POST["{$key}_field"])) {
                $student->$key = strval($_POST["{$key}_field"]);
            }
        }
        return $student;
    }
}