<?php

namespace App;

use App\Exception\ConfigValueException;

/**
 * Application configuration file, can use default settings, or load an .ini file.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 */
class Config {

    /** @var string $dbHost Database IP address. */
    protected $dbHost = "127.0.0.1";
    /** @var string $dbPort Database port. */
    protected $dbPort = "5412";
    /** @var string $dbUsername Database user. */
    protected $dbUsername = "root";
    /** @var string $dbPassword Database password. */
    protected $dbPassword = "qwerty";
    /** @var string $dbName Database name. */
    protected $dbName = "students";

    /** @var int $pagerElemPerPage Elements per page. */
    protected $pagerElemPerPage = 15;

    /**
     * Loads config from a .ini file.
     *
     * @param string $file A file to load.
     *
     * @return void
     */
    public function loadFromFile($file) {
        $ini = parse_ini_file($file, true);
        foreach($ini as $section => $container) {
            foreach($container as $name => $value) {
                $this->{$section . ucfirst($name)} = $value;
            }
        }
    }

    /**
     * Returns a value form a config.
     *
     * @param string $section Config section.
     * @param string $key Config value key.
     *
     * @throws ConfigValueException if value can't be found.
     *
     * @return string
     */
    public function getValue($section, $key) {
        if(isset($this->{$section . ucfirst($key)})) {
            return $this->{$section . ucfirst($key)};
        } else {
            throw new ConfigValueException("No such value in the config (Section: $section; Key: $key).");
        }
    }

}
