<?php

namespace App\Helper;

use \App\Model\Student;
use \App\Database\StudentDataGateway;

class AuthHelper {

    private $dataGateway;

    public function __construct(StudentDataGateway $dataGateway) {
        $this->dataGateway = $dataGateway;
    }

    public function getAuthorizedStudent() {
        if(isset($_COOKIE['auth'])) {
            $student = $this->dataGateway->selectStudent($_COOKIE['auth']);
            if($student != null) {
                return $student;
            }
        }
        return null;
    }

    public function authorizeStudent(Student $student) {
        if($student->token != null) {
            setcookie("auth", $student->token, time()+36000000);
            return true;
        }
        return false;
    }

    public function isAuthorized() {
        if(isset($_COOKIE['auth'])) {
            $student = $this->dataGateway->selectStudent($_COOKIE['auth']);
            if($student != null) {
                return true;
            }
        }
        return false;
    }

    public function createAuthToken() {
        $generator = new TokenGenerator();
        $token = $generator->generateToken(45);
        return $token;
    }

    public function logOut() {
        setcookie('auth', "", 0);
    }
}