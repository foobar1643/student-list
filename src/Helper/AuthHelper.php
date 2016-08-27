<?php

namespace App\Helper;

use App\Entity\Student;
use App\Database\StudentDataGateway;

class AuthHelper
{
    private $dataGateway;
    private $authorizedUser;

    public function __construct(StudentDataGateway $dataGateway)
    {
        $this->dataGateway = $dataGateway;
        $this->authorizedUser = null;
    }

    public function getAuthorizedUser()
    {
        if(isset($_COOKIE['auth']) && $this->authorizedUser === null) {
            $this->authorizedUser = $this->dataGateway->selectStudent($_COOKIE['auth']);
        }
        return ($this->authorizedUser === null) ? false : $this->authorizedUser;
    }

    public function authorizeStudent(Student $student)
    {
        return setcookie("auth", $student->getToken(), time()+36000000);
    }

    public function createAuthToken() {
        return TokenGenerator::generateToken(45);
    }

    public function logOut() {
        setcookie('auth', "", 0);
    }
}