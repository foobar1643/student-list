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
 * @todo Should I encapsulate the methods?
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

    protected function validateEmail($email)
    {
        //if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //    return "E-mail must be in name@example.com format.";
        //}
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