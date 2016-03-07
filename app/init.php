<?php
require("../vendor/autoload.php");

use Pimple\Container;
use \App\Config;
use \App\Database\StudentDataGateway;
use \App\ExceptionHandler;
use \App\Exception\FatalException;

$container = new Container();
$container["config"] = function($c) {
    $config = new Config();
    $config->loadFromFile("../config.ini");
    return $config;
};

$container["pdo"] = function($c) {
    $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s",
        $c["config"]->getValue('db', 'host'),
        $c["config"]->getValue('db', 'port'),
        $c["config"]->getValue('db', 'name'));
    $pdo = new \PDO($dsn,
        $c["config"]->getValue('db', 'username'),
        $c["config"]->getValue('db', 'password'));
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
};

$container["dataGateway"] = function($c) {
    return new StudentDataGateway($c["pdo"]);
};

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