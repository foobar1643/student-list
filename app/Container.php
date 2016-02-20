<?php

namespace App;
use \App\Config;
use \App\Database\StudentDataGateway;

class Container {

    protected $cfg;
    protected $studentDataGateway;

    private function getPDO() {
        $pdo = new \PDO($this->cfg->getValue('db', 'type') . ":dbname=" . $this->cfg->getValue('db', 'name') . ";host=" . $this->cfg->getValue('db', 'host'),
            $this->cfg->getValue('db', 'username'), $this->cfg->getValue('db', 'password'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function __construct(Config $config) {
        $this->cfg = $config;
        $this->studentDataGateway = new StudentDataGateway($this->getPDO());
    }

    public function getConfig() {
        return $this->cfg;
    }

    public function getDataGateway() {
        return $this->studentDataGateway;
    }

}