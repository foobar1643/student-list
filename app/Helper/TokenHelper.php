<?php

namespace App\Helper;

class TokenHelper {

    public function generateToken($length) {
        $source = str_split('abcdefghijklmnopqrstuvwxyz'
          .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
          .'0123456789');
        for($i = 0; $i < $length; $i++) {
            $result .= $source[rand(0, count($source) - 1)];
        }
        return $result;
    }

    public function getAuthToken() {
        return $_COOKIE['auth'];
    }

    public function getCsrfToken() {
        return $_COOKIE['token'];
    }

    public function validateToken($token) {
        if(preg_match("/(^[a-zA-Z0-9]{45}$)/", $token)) {
            return true;
        }
        return false;
    }

    public function setCsrfToken() {
        $currentToken = null;
        if(isset($_COOKIE['token'])) {
            $currentToken = $_COOKIE['token'];
        } else {
            $currentToken = $this->generateToken(45);
        }
        setcookie("token", $currentToken, time()+36000000);
        return $currentToken;
    }

    public function setAuthToken() {
        $currentToken = null;
        if(isset($_COOKIE['auth']) && $this->validateToken($_COOKIE['auth'])) {
            $currentToken = $_COOKIE['auth'];
        } else {
            $currentToken = $this->generateToken(45);
        }
        setcookie("auth", $currentToken, time()+36000000);
        return $currentToken;
    }

    public function checkCsrfToken($formToken) {
        if(isset($_COOKIE['token']) && $_COOKIE['token'] == $formToken) {
            return true;
        }
        return false;
    }

    public function unsetAuthToken() {
        setcookie('auth', null, -1);
    }
}