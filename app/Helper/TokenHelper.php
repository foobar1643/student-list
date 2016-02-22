<?php

namespace App\Helper;

class TokenHelper {

    public function generateToken() {
        $source = str_split('abcdefghijklmnopqrstuvwxyz'
          .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
          .'0123456789');
        for($i = 0; $i < 40; $i++) {
            $result .= $source[rand(0, count($source)-1)];
        }
        return $result;
    }

    public function setCsrfToken($token) {
        setcookie("token", $token, time()+36000000);
    }

    public function checkCsrfToken($formData, $cookieData) {
        if(isset($formData) && isset($cookieData) && $formData == $cookieData) {
            return true;
        }
        return false;
    }
}