<?php

namespace App\Controller;

use Pimple\Container;
use App\Entity\Student;
use App\Helper\RegistrationHelper;

/**
 * Callable, displays a student add/edit form, also processes POST requests from that form.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 */
class ControllerForm implements AppController
{
    /** @var Container $container DI container. */
    private $container;
    /** @var RegistrationHelper $regHelper RegistrationHelper instance. */
    private $regHelper;

    /**
     * Constructor.
     *
     * @param Container $c DI container.
     */
    public function __construct(Container $c)
    {
        $this->container = $c;
        $this->regHelper = $c['registrationHelper'];
    }

    /**
     * A method that allows to use this class as a callable.
     *
     * @return void
     */
    public function __invoke()
    {
        $isAuthorized = $this->container["authHelper"]->getAuthorizedUser();
        $csrfToken = $this->container["csrfHelper"]->setCsrfToken();
        $student = ($isAuthorized) ? $this->container["authHelper"]->getAuthorizedUser() : new Student();
        if($_SERVER['REQUEST_METHOD'] === "POST") {
            $errors = $this->processPostRequest($student);
            if($errors === true) { // If there is no errors in form, redirect user to index.php
                header("Location: index.php?notify=success");
            }
        }
        include("../templates/form.phtml");
    }

    /**
     * Processes POST request.
     *
     * @todo CSRF protection.
     *
     * @param Student $student Student object to validate.
     *
     * @return bool|array
     */
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

    /**
     * Gets POST data and returns is as a Student object.
     *
     * @param Student $student Student object to return.
     *
     * @return Student
     */
    private function getFormData(Student $student) {
        foreach($this->regHelper->getAllowedFields() as $key) {
            $entitySetter = sprintf('set%s', ucfirst($key));
            $student->$entitySetter(isset($_POST["{$key}_field"]) ? strval($_POST["{$key}_field"]) : null);
        }
        return $student;
    }
}