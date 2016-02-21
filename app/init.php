<?php
require("../vendor/autoload.php");

use \App\Container;
use \App\Config;
use \App\Exception\ExceptionHandler;
use \App\Exception\FatalException;

$c = new Config();
$c->loadFromFile();
$container = new Container($c);

function runApp($app) {
    $handler = new ExceptionHandler();
    try {
        $app->run();
    } catch(PDOException $e) {
        $handler->handleException($e);
    } catch(FatalException $e) {
        $handler->handleException($e);
    }
}