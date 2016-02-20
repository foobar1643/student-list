<?php

namespace App;
use \App\Exception\ConfigValueException;

class Config {

    /* App config variables */
    protected $db_type;
    protected $db_host;
    protected $db_port;
    protected $db_username;
    protected $db_password;
    protected $db_name;

    protected $pager_elemPerPage;

    public function __construct() {
        // Default settings
        $this->db_type = "pgsql";
        $this->db_host = "127.0.0.1";
        $this->db_port = "5412";
        $this->db_username = "root";
        $this->db_password = "qwerty";
        $this->db_name = "students";

        $this->pager_elemPerPage = 15;
    }

    public function loadFromFile() {
        $ini = parse_ini_file("../config.ini", true);
        foreach($ini as $section => $container) {
            foreach($container as $name => $value) {
                $this->{$section."_".$name} = $value;
            }
        }
    }

    public function getValue($section, $key) {
        if(isset($this->{$section."_".$key})) {
            return $this->{$section."_".$key};
        } else {
            throw new ConfigValueException("No such value in the config (Section: $section; Key: $key).");
        }
    }

}
