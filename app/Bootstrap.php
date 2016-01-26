<?php

namespace App;

class Bootstrap {

    public static function getPDO() {
        $config = new \App\Config();
        $pdo = new \PDO($config->getKey('database', 'type') . ":dbname=" . $config->getKey('database', 'name') . ";host=" . $config->getKey('database', 'host'),
            $config->getKey('database', 'username'), $config->getKey('database', 'password'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

}