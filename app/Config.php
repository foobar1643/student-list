<?php

namespace App;

use \App\Exception\ConfigValueException;

class Config {

    /* Default settings */
    protected $dbHost = "127.0.0.1";
    protected $dbPort = "5412";
    protected $dbUsername = "root";
    protected $dbPassword = "qwerty";
    protected $dbName = "students";

    protected $pagerElemPerPage = 15;

    public function loadFromFile($file) {
        $ini = parse_ini_file($file, true);
        foreach($ini as $section => $container) {
            foreach($container as $name => $value) {
                $this->{$section . ucfirst($name)} = $value;
            }
        }
    }

    public function getValue($section, $key) {
        if(isset($this->{$section . ucfirst($key)})) {
            return $this->{$section . ucfirst($key)};
        } else {
            throw new ConfigValueException("No such value in the config (Section: $section; Key: $key).");
        }
    }

}
