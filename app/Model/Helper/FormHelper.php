<?php

namespace App\Model\Helper;
use \App\Model\Student;

class FormHelper {

    public function validateForm($data) {
        $errors = null;
        if(!preg_match("/[А-ЯЁA-Z]{1}[а-яёa-z]{1,15}$/u", $data['name_field'])) $errors['name'] = true;
        if(!preg_match("/[А-ЯЁA-Z]{1}[а-яёa-z]{1,15}$/u", $data['surname_field'])) $errors['surname'] = true;
        if($data['gender_field'] != "male" && $data['gender_field'] != "female") $errors['gender'] = true;
        if(!filter_var($data['email_field'], FILTER_VALIDATE_EMAIL)) $errors['email'] = true;
        if(!preg_match("/^[А-ЯЁа-яёa-zA-Z0-9]{2,5}$/u", $data['sgroup_field'])) $errors['group'] = true;
        if(!preg_match("/[1][9][0-9]{2}$/", $data['byear_field'])) $errors['byear'] = true;
        if($data['status_field'] != "nonresident" && $data['status_field'] != "resident") $errors['status'] = true;
        if($data['rating_field'] > 150 || $data['rating_field'] <= 0) $errors['rating'] = true;
        return $errors;
    }
}