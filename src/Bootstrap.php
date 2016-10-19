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
use Students\Helper\CSRFProtection;
use Students\Helper\StudentAuthorization;
use Students\Utility\View;

$services['config'] = function($c) {
    $configuration = new Configuration();
    $configuration->loadFromFile(__DIR__ . '/../db.ini');
    return $configuration;
};

$services['pdo'] = function($c) {
    $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s",
        $c['config']->getValue('Database', 'host'),
        $c['config']->getValue('Database', 'port'),
        $c['config']->getValue('Database', 'name'));
    $pdo = new \PDO($dsn,
        $c['config']->getValue('Database', 'username'),
        $c['config']->getValue('Database', 'password'));
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

$services['csrfProtection'] = function($c) {
    return new CSRFProtection();
};

$container = new Container($services);