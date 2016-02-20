<?php

namespace App\Exception;

class ExceptionHandler {

    public function handleException($e) {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        error_log($e->getMessage(), 0);
        include("../templates/error.html");
    }
}