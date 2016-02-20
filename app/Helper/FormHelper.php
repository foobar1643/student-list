<?php

namespace App\Helper;
use \App\Model\Student;

class FormHelper {

    public function validateStudent(Student $student) {
        $errors = null;
        if(!preg_match("/^[А-ЯЁA-Z]{1}[-а-яёa-zА-ЯЁA-Z[:space:]]{1,15}$/u", $student->name)) $errors['name'] = true;
        if(!preg_match("/^[А-ЯЁA-Z]{1}[-а-яёa-zА-ЯЁA-Z[:space:]]{1,20}$/u", $student->surname)) $errors['surname'] = true;
        if($student->gender != Student::GENDER_MALE && Student::GENDER_FEMALE != "female") $errors['gender'] = true;
        if(!filter_var($student->email, FILTER_VALIDATE_EMAIL)) $errors['email'] = true;
        if(!preg_match("/^[-А-ЯЁа-яёa-zA-Z0-9]{2,5}$/u", $student->sgroup)) $errors['group'] = true;
        if(!preg_match("/^[1][9][0-9]{2}$/", $student->byear)) $errors['byear'] = true;
        if($student->status != "nonresident" && $student->status != "resident") $errors['status'] = true;
        if($student->rating > 150 || $student->rating <= 0) $errors['rating'] = true;
        return $errors;
    }

    public function getAllowedFields() {
        return ['id', 'name', 'surname', 'gender', 'sgroup', 'email',
            'byear', 'status', 'rating', 'token'];
    }
}