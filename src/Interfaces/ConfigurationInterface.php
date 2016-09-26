<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Interfaces
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Interfaces;

/**
 * Describes application configuration class.
 */
interface ConfigurationInterface
{
    /**
     * Loads configuration from .ini file.
     *
     * If $preserveValues equals TRUE, the implementation must merge any existing
     * values with the loaded ones. If both configurations have the same string keys,
     * then the later value for that key should overwrite the previous one.
     * If, however, the configurations contain numeric keys, the later value should not
     * overwrite the original value, but should be appended.
     *
     * This method should throw a InvalidArgumentException if given file does not exists,
     * or if the file is not ini.
     *
     * This method should throw a RuntimeException if an error occured while processing given
     * configuration file (parse_ini_file returns FALSE, for example).
     *
     * @param string $filename Name of the file to load.
     * @param bool $preserveValues If true, implementations must merge any
     * existing values with loaded ones. If false - any existing values should be
     * overwritten with the loaded ones. Default value is false.
     *
     * @throws \InvalidArgumentException If configuration file is invalid or not found.
     * @throws \RuntimeException If an error occured while loading the configuration file.
     * @throws \UnexpectedValueException If a value in the file does not have a classfield to store it.
     *
     * @return void
     */
    public function loadFromFile($filename, $preserveValues = false);

    /**
     * Loads configuration from PHP array.
     *
     * If $preserveValues equals TRUE, the implementation must merge any existing
     * values with the loaded ones. If both configurations have the same string keys,
     * then the later value for that key should overwrite the previous one.
     * If, however, the configurations contain numeric keys, the later value should not
     * overwrite the original value, but should be appended.
     *
     * @param array $data Configuration in a form of associative array.
     * @param bool $preserveValues If true, implementations must merge any
     * existing values with loaded ones. If false - any existing values should be
     * overwritten with the loaded ones. Default value is false.
     *
     * @return void
     */
    public function loadFromArray(array $data, $preserveValues = false);

    /**
     * Returns a value for given key.
     *
     * This method should throw InvalidArgumentException if value is not found
     * in the config.
     *
     * @param string $section Section in the configuration.
     * @param string $key A key in the configuration.
     *
     * @throws \InvalidArgumentException If given key is not in the config.
     *
     * @return mixed Value for given key.
     */
    public function getValue($section, $key);

    /**
     * Returns an array with section values.
     *
     * This method should throw InvalidArgumentException if section is not in the config.
     *
     * @param string $section Section to fetch.
     *
     * @throws \InvalidArgumentException If section with a given name is not in the config.
     *
     * @return array Associative array with section parameters.
     */
    public function getSection($section);

    /**
     * Returns all sections with their values in a form of associative array.
     *
     * @return array Associative array with all sections and their values.
     */
    public function getAll();

    /**
     * Checks if a section with given name exists in the config.
     *
     * @param string $section Section to check.
     *
     * @return boolean
     */
    public function hasSection($section);

    /**
     * Returns true if a key with a given name exists in the given section.
     * Returns false otherwise.
     *
     * @param string $key Key to check.
     *
     * @return boolean
     */
    public function hasKey($section, $key);
}