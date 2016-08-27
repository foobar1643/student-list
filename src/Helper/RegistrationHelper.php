<?php

namespace App\Helper;

use App\Entity\Student;
use App\Database\StudentDataGateway;

class RegistrationHelper {

    const MAX_RATING = 300;

    private $dataGateway;

    public function __construct(StudentDataGateway $gateway) {
        $this->dataGateway = $gateway;
    }

    public function validateStudent(Student $student) {
        $errors = null;
        if(!preg_match("/^[А-ЯЁA-Z][-а-яёa-zА-ЯЁA-Z\\s]{1,15}$/u", $student->getName())) {
            $errors['name'] = "Имя должно начинаться с большой буквы, быть не длиннее 15 символов, в имени "
            ."разрешается использовать символы латиницы, кириллицы, цифры, дефисы и пробелы.";
        }
        if(!preg_match("/^[А-ЯЁA-Z][-'`а-яёa-zА-ЯЁA-Z\\s]{1,20}$/u", $student->getSurname())) {
            $errors['surname'] = "Фамилия должна начинаться с большой буквы, быть не длиннее 20 символов, в фамилии "
            ."разрешается использовать символы латиницы, кириллицы, апострофы, дефисы и пробелы.";
        }
        if($student->getGender() != Student::GENDER_MALE && $student->getGender() != Student::GENDER_FEMALE) {
            $errors['gender'] = "Пол должен быть выбран из списка.";
        }
        if(!filter_var($student->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "E-mail должен быть указан в формате name@example.com.";
        }
        if(!preg_match("/^[-А-ЯЁа-яёa-zA-Z0-9]{2,5}$/u", $student->getGroup())) {
            $errors['group'] = "Имя группы должно быть не короче двух и не длиннее пяти символов, "
            ."в имени группы разрешается использовать символы латиницы, кириллицы, цифры и дефисы.";
        }
        if(!preg_match("/^19[0-9]{2}$/", $student->getBirthYear())) {
            $errors['byear'] = "Год рождения должен быть не меньше 1900 и не больше 2000.";
        }
        if($student->getStatus() != Student::STATUS_RESIDENT && $student->getStatus() != Student::STATUS_NONRESIDENT) {
            $errors['status'] = "Статус должен быть выбран из списка.";
        }
        if($student->getRating() > self::MAX_RATING || $student->getRating() < 0) {
            $errors['rating'] = "Баллы должны быть больше 0 и не больше чем " . self::MAX_RATING . ".";
        }
        if($this->dataGateway->checkEmail($student->getEmail(), $student->getToken()) != 0) {
            $errors['email'] = "Такой e-mail уже занят другим пользователем.";
        }
        return $errors;
    }

    public function getAllowedFields() {
        return ['name', 'surname', 'gender', 'group', 'email',
            'birthYear', 'status', 'rating'];
    }
}