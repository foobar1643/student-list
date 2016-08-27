<?php

namespace App\Helper;

class CsrfHelper
{
    public function validateCsrfToken($token)
    {
        return (preg_match("/(^[a-zA-Z0-9]{45}$)/", $token));
    }

    public function checkCsrfToken($formToken)
    {
        return (isset($_COOKIE['token']) && $_COOKIE['token'] === $formToken);
    }

    public function setCsrfToken()
    {
        $currentToken = isset($_COOKIE['token']) ? $_COOKIE['token'] : TokenGenerator::generateToken(45);
        setcookie("token", $currentToken, time()+36000000);
        return $currentToken;
    }
}