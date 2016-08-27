<?php

namespace App\Controller;

use Pimple\Container;
use App\Entity\Student;
use App\Helper\RegistrationHelper;

class ControllerForm implements AppController
{
    private $container;
    private $regHelper;

    public function __construct(Container $c)
    {
        $this->container = $c;
        $this->regHelper = $c['registrationHelper'];
    }

    public function __invoke()
    {
        $isAuthorized = $this->container["authHelper"]->getAuthorizedUser();
        $csrfToken = $this->container["csrfHelper"]->setCsrfToken();
        $student = ($isAuthorized) ? $this->container["authHelper"]->getAuthorizedUser() : new Student();
        if($_SERVER['REQUEST_METHOD'] === "POST") {
            $errors = $this->processPostRequest($student);
            if($errors === true) { // If there is no errors in form
                header("Location: index.php?notify=success");
            }
        }
        include("../templates/form.phtml");
    }

    private function processPostRequest(Student $student)
    {
        $dataGateway = $this->container['dataGateway'];
        $student = $this->getFormData($student);
        $errors = $this->regHelper->validateStudent($student);
        if(!$errors) {
            if($this->container["authHelper"]->getAuthorizedUser()) {
                $dataGateway->updateStudent($student);
            } else {
                $student->setToken($this->container["authHelper"]->createAuthToken());
                $dataGateway->addStudent($student);
                $this->container["authHelper"]->authorizeStudent($student);
            }
            return true;
        }
        return $errors;
    }

    private function getFormData(Student $student) {
        foreach($this->regHelper->getAllowedFields() as $key) {
            $entitySetter = sprintf('set%s', ucfirst($key));
            $student->$entitySetter(isset($_POST["{$key}_field"]) ? strval($_POST["{$key}_field"]) : null);
        }
        return $student;
    }
}