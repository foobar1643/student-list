<?php
require_once("../vendor/autoload.php");

use \App\Controller\ControllerForm;
use \App\ExceptionHandler;

$app = new ControllerForm();

try {
    $app->run();
} catch(PDOException $e) {
    $handler = new ExceptionHandler();
    $handler->handleException($e);
}
