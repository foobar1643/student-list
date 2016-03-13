<?php

namespace App\Helper;

class CsrfHelper {

    public function getCsrfToken() {
        return $_COOKIE['token'];
    }

    public function validateCsrfToken($token) {
        if(preg_match("/(^[a-zA-Z0-9]{45}$)/", $token)) {
            return true;
        }
        return false;
    }

    public function checkCsrfToken($formToken) {
        if(isset($_COOKIE['token']) && $_COOKIE['token'] == $formToken) {
            return true;
        }
        return false;
    }

    public function setCsrfToken() {
        $currentToken = null;
        $generator = new TokenGenerator();
        if(isset($_COOKIE['token'])) {
            $currentToken = $_COOKIE['token'];
        } else {
            $currentToken = $generator->generateToken(45);
        }
        setcookie("token", $currentToken, time()+36000000);
        return $currentToken;
    }
}