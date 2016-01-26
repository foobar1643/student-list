<?php

namespace App;

class Config {

    protected $config;

    public function __construct() {
        $this->config = parse_ini_file("../config.ini", true);
    }

    public function getKey($section, $key) {
        if(isset($this->config[$section][$key])) {
            return $this->config[$section][$key];
        }
        return false;
    }

}
