<?php

namespace App;

class ExceptionHandler {

    public function handleException($e) {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        error_log($e->__toString(), 0);
        include("../templates/error.html");
    }
}