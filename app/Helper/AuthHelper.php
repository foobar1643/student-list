<?php

namespace App\Helper;

use \App\Database\StudentDataGateway;

class AuthHelper {

    private $dataGateway;

    public function __construct(StudentDataGateway $dataGateway) {
        $this->dataGateway = $dataGateway;
    }

    public function getAuthorizedStudent() {
        if(isset($_COOKIE['auth'])) {
            $student = $this->dataGateway->selectStudent($_COOKIE['auth']);
            if($student->id != null) {
                return $student;
            }
        }
        return false;
    }

    public function isAuthorized() {
        if(isset($_COOKIE['auth'])) {
            return true;
        }
        return false;
    }

    public function authorizeStudent() {
        $generator = new TokenGenerator();
        $token = $generator->generateToken(45);
        setcookie("auth", $token, time()+36000000);
        return $token;
    }

    public function logOut() {
        setcookie('auth', null, -1);
    }
}