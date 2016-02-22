<?php

namespace App\Helper;
use \App\Model\Student;

class FormHelper {

    const MAX_RATING = 300;

    public function validateStudent(Student $student) {
        $errors = null;
        if(!preg_match("/^[А-ЯЁA-Z]{1}[-а-яёa-zА-ЯЁA-Z\\s]{1,15}$/u", $student->name)) {
            $errors['name'] = "Имя должно быть не длиннее 15 символов, в имени "
            ."разрешается использовать символы латиницы, кириллицы, цифры, дефисы и пробелы.";
        }
        if(!preg_match("/^[А-ЯЁA-Z]{1}[-а-яёa-zА-ЯЁA-Z\\s]{1,20}$/u", $student->surname)) {
            $errors['surname'] = "Фамилия должна быть не длиннее 20 символов, в фамилии "
            ."разрешается использовать символы латиницы, кириллицы, цифры, дефисы и пробелы.";
        }
        if($student->gender != Student::GENDER_MALE && Student::GENDER_FEMALE != "female") {
            $errors['gender'] = "Пол должен быть выбран из списка.";
        }
        if(!filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "E-mail должен быть указан в формате name@site.com.";
        }
        if(!preg_match("/^[-А-ЯЁа-яёa-zA-Z0-9]{2,5}$/u", $student->sgroup)) {
            $errors['group'] = "Имя группы должно быть не короче двух и не длиннее пяти символов, "
            ."в имени группы разрешается использовать символы латиницы, кириллицы, цифры и дефисы.";
        }
        if(!preg_match("/^[1][9][0-9]{2}$/", $student->byear)) {
            $errors['byear'] = "Год рождения должен быть не меньше 1900 и не больше 2000.";
        }
        if($student->status != Student::STATUS_RESIDENT && $student->status != Student::STATUS_NONRESIDENT) {
            $errors['status'] = "Статус должен быть выбран из списка.";
        }
        if($student->rating > self::MAX_RATING || $student->rating < 0) {
            $errors['rating'] = "Баллы должны быть больше 0 и не больше чем " . self::MAX_RATING . ".";
        }
        return $errors;
    }

    public function getAllowedFields() {
        return ['id', 'name', 'surname', 'gender', 'sgroup', 'email',
            'byear', 'status', 'rating', 'token'];
    }
}