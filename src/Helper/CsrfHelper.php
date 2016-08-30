<?php

namespace App\Helper;

/**
 * Provides basic protection against CSRF exploit.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 */
class CsrfHelper
{
    /**
     * Validates CSRF token.
     *
     * @return bool
     */
    public function checkCsrfToken($formToken)
    {
        return (isset($_COOKIE['token']) && $_COOKIE['token'] === $formToken);
    }

    /**
     * If user already has a CSRF token - refreshes the cookie expires date, if not - generates a new token.
     *
     * @return string
     */
    public function setCsrfToken()
    {
        $currentToken = isset($_COOKIE['token']) ? $_COOKIE['token'] : TokenGenerator::generateToken(45);
        setcookie("token", $currentToken, time()+36000000);
        return $currentToken;
    }
}