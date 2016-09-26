<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Entity
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Entity;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Student entity.
 */
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

    public static function fromPostRequest(ServerRequestInterface $request)
    {
        $allowedFields = ['name', 'lastName', 'gender', 'group', 'email',
            'sgroupbyear', 'status', 'rating'];

        $body = $request->getParsedBody();
        $student = new Student();
        foreach($allowedFields as $key) {
            $setter = sprintf('set%s', ucfirst($key));
            $student->$setter(isset($body[$key]) ? strval($body[$key]) : null);
        }
        return $student;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFirstName()
    {
        return $this->name;
    }

    public function setFirstName($name)
    {
        $this->name = $name;
    }

    public function getLastName()
    {
        return $this->surname;
    }

    public function setLastName($name)
    {
        $this->surname = $name;
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