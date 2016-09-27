<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

require(__DIR__ . '/../vendor/autoload.php');

use Pimple\Container;
use Students\Utility\Configuration;
use Students\Database\StudentDataGateway;
use Students\Validation\StudentValidator;
use Students\Helper\StudentAuthorization;
use Students\Utility\View;

$services['config'] = function($c) {
    // Initialize configuration class
    $configuration = new Configuration();
    // Load configuration from 'config.ini' in app root directory.
    $configuration->loadFromFile(__DIR__ . '/../db.ini');
    return $configuration;
};

$services['pdo'] = function($c) {
    // Creates a DSN string using values from config.
    $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s",
        $c['config']->getValue('Database', 'host'),
        $c['config']->getValue('Database', 'port'),
        $c['config']->getValue('Database', 'name'));
    // Creates a PDO object using DSN string and username\password values from
    // application config.
    $pdo = new \PDO($dsn,
        $c['config']->getValue('Database', 'username'),
        $c['config']->getValue('Database', 'password'));
    // Tells PDO to throw an exception if an error occured.
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
};

$services['studentGateway'] = function($c) {
    return new StudentDataGateway($c['pdo']);
};

$services['view'] = function($c) {
    return new View(__DIR__ . '/../templates');
};

$services['studentValidator'] = function($c) {
    return new StudentValidator($c['studentGateway']);
};

$services['studentAuthorization'] = function($c) {
    return new StudentAuthorization();
};

$container = new Container($services);