<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Validation
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Validation;

/**
 * Abstract validator class. Contains basic validation rules for names, groups
 * and emails.
 *
 * (New) Validator concept:
 *
 * I want to make a simple but flexible Validator class. The key to flexibilty
 * would be user defined custom set of rules that would be used for validation.
 * Validation rules would probably be represented as an associative array. I also tought
 * about storing the rules as an object or collection, but I don't think that would benefit
 * the validator much, it will only make code more complicated.
 *
 * Here's an example that shows how the ruleset would be defined. In this example, I'm validating a 'Student'
 * entity.
 *
 * $rules['Student'] // Array index 'Student' represents name of the entity that current ruleset would apply to.
 * If a collision occurs (ruleset for this entity already exists), validator should raise an exception.
 *
 * $rules['Student']['firstName'] // Array index 'firstName' represents name of the class field that current
 * rule would apply to.
 * If given entity doesn't have a classfield with this name, validator should raise an exception.
 * If a collision occurs, validator should raise an exception.
 *
 * $rules['Student']['firstName'] = [ // A validation rule for a classfield would be represented as an associative array.
 *      'regexp' => '/^[А-ЯЁA-Z][-а-яёa-zА-ЯЁA-Z\\s]{1,20}$/u' // 'regexp' element contains a regular expression
 *      // that will be used to match a classfield value using preg_match.
 *      'message' => 'First name is invalid.' // 'message' element contains a message that would be returned
 *      // by validator if validation of current class field failed.
 * ]
 *
 * $rules['Student']['lastName'] = [
 *      'inherit' => 'firstName' // 'inherit' element tells validator to inherit validation rules from
 *      // an element with given name. If there is no rule with a given name, validator should raise an exception.
 *      'message' => 'Last name is invalid.' // Note that this would overwrite the inherited value from 'firstName'
 *      // validation rule. This could be done with any inherited element.
 * ]
 *
 * $rules['Student']['gender'] = [
 *      'enum' => ['male', 'female'] // 'enum' element contains an array of values that will be used to match a
 *      // classfield value using a strict comparison operator (===).
 * ]
 *
 * $rules['Student']['birthYear'] = [
 *      'min' => 1900 // 'min' element contains a value that would be considered as minimum to pass a validation.
 *      'max' => 2000 // 'max' element contains a value that would be considered as maximum to pass a validation.
 * ]
 *
 * These are just basic validation elements, another point of flexibilty is that any developer can extend the
 * validator by adding their own elements.
 */
abstract class Validator
{
    protected function validateName($name)
    {
        if(!preg_match("/^[А-ЯЁA-Z][-а-яёa-zА-ЯЁA-Z\\s]{1,20}$/u", $name)) {
            return "Name must begin with a capital letter, it must be shorter"
                ."than 20 symblos, first name can consist of latin, cyrillc"
                ."symbols, apostrophes, hyphens and spaces.";
        }
        return true;
    }

    /**
     * @todo Change the way email is validated, filter_var doesn't work with non-latin
     * symbols (for example ivan@пример.рф).
     */
    protected function validateEmail($email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "E-mail must be in name@example.com format.";
        }
        return true;
    }

    protected function validateGroup($group)
    {
        if(!preg_match("/^[-А-ЯЁа-яёa-zA-Z0-9]{2,5}$/u", $group)) {
            return "Group name must be longer than 2 and not longer than 5 symbols,"
                ." group name can consist of latin, cyrillic symols, numbers and hyphens.";
        }
        return true;
    }

    protected function filterErrors($error)
    {
        return ($error !== true);
    }
}