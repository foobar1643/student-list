<?php
require_once("../vendor/autoload.php");

use \App\Controller\ControllerIndex;
use \App\ExceptionHandler;

$app = new ControllerIndex();
$test = 1;
try {
    $app->run();
} catch(PDOException $e) {
    $handler = new ExceptionHandler();
    $handler->handleException($e);
}