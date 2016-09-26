<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Utility
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Utility;

use Students\Interfaces\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Array with configuration values.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Retains immutability.
     * This method does nothing.
     */
    public function __set($name, $value) { }

    /**
     * Loads configuration from .ini file.
     *
     * If $preserveValues equals TRUE, this method will merge any existing
     * values with the loaded ones. If both configurations have the same string keys,
     * then the later value for that key will overwrite the previous one.
     * If, however, the configurations contain numeric keys, the later value will not
     * overwrite the original value, but will be appended.
     *
     * @param string $filename Name of the file to load.
     * @param bool $preserveValues If true, any existing values will be merged
     * with loaded ones. If false - any existing values will be overwritten
     * with the loaded ones. Default value is false.
     *
     * @throws \InvalidArgumentException If configuration file is invalid or not found.
     * @throws \RuntimeException If an error occured while loading the configuration file.
     *
     * @return void
     */
    public function loadFromFile($filename, $preserveValues = false)
    {
        // Throws an exception if file with a given name does not exist.
        if(!file_exists($filename)) {
            throw new InvalidArgumentException("Configuration file '{$filename}' does not exists.");
        }

        // Attempts to parse INI file
        $data = parse_ini_file($filename, true);

        // Throws an exception if an error occured while parsing INI file.
        if($data === false) {
            throw new RuntimeException("Failed to read configuration file '{$filename}'.");
        }

        $this->loadFromArray($data, $preserveValues);
    }

    /**
     * Loads configuration from PHP array.
     *
     * If $preserveValues equals TRUE, this method will merge any existing
     * values with the loaded ones. If both configurations have the same string keys,
     * then the later value for that key will overwrite the previous one.
     * If, however, the configurations contain numeric keys, the later value will not
     * overwrite the original value, but will be appended.
     *
     * @param array $data Configuration in a form of associative array.
     * @param bool $preserveValues If true, any existing values will be merged
     * with loaded ones. If false - any existing values will be overwritten
     * with the loaded ones. Default value is false.
     *
     * @return void
     */
    public function loadFromArray(array $data, $preserveValues = false)
    {
        // If preserveValues is set to true and current config is not empty -
        // merges existing configuration array with the loaded one.
        $this->config = ($preserveValues === true && !empty($this->config))
            ? array_merge($data, $this->config)
            : $data;
    }

    /**
     * Returns a value for given key.
     *
     * @param string $section Case-sensetive section name in the configuration.
     * @param string $key Case-sensetive key name in the configuration.
     *
     * @throws \InvalidArgumentException If given section or key is not in the config.
     *
     * @return mixed Value for given key.
     */
    public function getValue($section, $key)
    {
        return $this->config[$section][$key];
    }

    /**
     * Returns an array with section values.
     *
     * @param string $section Case-sensetive section name to fetch.
     *
     * @throws \InvalidArgumentException If section with a given name is not in the config.
     *
     * @return array Associative array with section values.
     */
    public function getSection($section)
    {
        if(!$this->hasSection($section)) {
            throw new \InvalidArgumentException("Can't find section {$section} in the configuration.");
        }
        return $this->config[$section];
    }

    /**
     * Returns all sections with their values in a form of associative array.
     *
     * @return array Associative array with all sections and their values.
     */
    public function getAll()
    {
        return $this->config;
    }

    /**
     * Checks if a section with given name exists in the config.
     *
     * @param string $section Case-sensetive section name to check.
     *
     * @return boolean True if section exists, false otherwise.
     */
    public function hasSection($section)
    {
        return array_key_exists($section, $this->config);
    }

    /**
     * Checks if a key with given name exists in the config.
     *
     * @param string $section Case-sensetive section name that contains the key.
     * @param string $key Case-sensetive key name to check.
     *
     * @return boolean True if key exists, false otherwise.
     */
    public function hasKey($section, $key)
    {
        return ($this->hasSection($section) && array_key_exists($key, $this->config[$section]));
    }
}