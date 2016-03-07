<?php

namespace App\Model;

class Student {

    const GENDER_MALE = "male";
    const GENDER_FEMALE = "female";
    const STATUS_RESIDENT = "resident";
    const STATUS_NONRESIDENT = "nonresident";

    public $id;
    public $name;
    public $surname;
    public $gender;
    public $sgroup;
    public $email;
    public $byear;
    public $status;
    public $rating;
    public $token;
}