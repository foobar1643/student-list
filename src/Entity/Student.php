<?php

namespace App\Entity;

class Student
{
    const GENDER_MALE = "male";
    const GENDER_FEMALE = "female";
    const STATUS_RESIDENT = "resident";
    const STATUS_NONRESIDENT = "nonresident";

    private $id;
    private $name;
    private $surname;
    private $gender;
    private $sgroup;
    private $email;
    private $byear;
    private $status;
    private $rating;
    private $token;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function getGroup()
    {
        return $this->sgroup;
    }

    public function setGroup($group)
    {
        $this->sgroup = $group;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getBirthYear()
    {
        return $this->byear;
    }

    public function setBirthYear($year)
    {
        $this->byear = $year;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
}