<?php
require("../vendor/autoload.php");

use Pimple\Container;
use \App\Config;
use \App\Database\StudentDataGateway;
use \App\Helper\CsrfHelper;
use \App\Helper\TableHelper;
use \App\Helper\AuthHelper;
use \App\Helper\RegistrationHelper;
use \App\Controller\AppController;
use \App\ExceptionHandler;

$container = new Container();
$handler = new ExceptionHandler();

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

$container["registrationHelper"] = function($c) {
    return new RegistrationHelper($c["dataGateway"]);
};

$container["authHelper"] = function($c) {
    return new AuthHelper($c["dataGateway"]);
};

$container["csrfHelper"] = function($c) {
    return new CsrfHelper();
};

$container["tableHelper"] = function($c) {
    return new TableHelper();
};

set_exception_handler(array($handler, 'handleException'));
set_error_handler(array($handler, 'exceptionErrorHandler'));

function runApp(AppController $app) {
    $app->run();
}