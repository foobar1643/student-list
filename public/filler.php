<?php
require_once("../vendor/autoload.php");

use \App\Controller\ControllerFiller;
use \App\ExceptionHandler;

$app = new ControllerFiller();

try {
    $app->run();
} catch(PDOException $e) {
    $handler = new ExceptionHandler();
    $handler->handleException($e);
}
