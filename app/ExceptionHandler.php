<?php

namespace App;

class ExceptionHandler {

    public function handleException($e) {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        include_once("../templates/error.html");
    }
}