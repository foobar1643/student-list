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

    /**
     * Student Id.
     * @var int
     */
    protected $id;

    /**
     * Student first name.
     * @var string
     */
    protected $name;

    /**
     * Student last name.
     * @var string
     */
    protected $surname;

    /**
     * Student gender.
     * @var string
     */
    protected $gender;

    /**
     * Student group.
     * @var string
     */
    protected $sgroup;

    /**
     * Student email.
     * @var string
     */
    protected $email;

    /**
     * Student birth year.
     * @var int
     */
    protected $byear;

    /**
     * Student status.
     * @var string
     */
    protected $status;

    /**
     * Student rating.
     * @var int
     */
    protected $rating;

    /**
     * Student authorization token.
     * @var string
     */
    protected $token;

    /**
     * Creates a Student entity from POST request.
     *
     * @param ServerRequestInterface $request An instance of PSR-7 HTTP request.
     *
     * @return Student Student entity created from post request.
     */
    public static function fromPostRequest(ServerRequestInterface $request)
    {
        $allowedFields = ['firstName', 'lastName', 'gender', 'group', 'email',
            'birthYear', 'status', 'rating'];

        $body = $request->getParsedBody();
        $student = new Student();
        foreach($allowedFields as $key) {
            $setter = sprintf('set%s', ucfirst($key));
            $student->$setter(isset($body[$key]) ? strval($body[$key]) : null);
        }
        return $student;
    }

    /**
     * Retrieves student id.
     *
     * @return int Student id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets id to a given value.
     *
     * @param $id Id value to set.
     *
     * @return static Instance of self with modified id.
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Retireves student first name.
     *
     * @return string Student first name.
     */
    public function getFirstName()
    {
        return $this->name;
    }

    /**
     * Sets first name to a given value.
     *
     * @param $name First name to set.
     *
     * @return static Instance of self with modified first name.
     */
    public function setFirstName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retrieves student last name.
     *
     * @return string Student last name.
     */
    public function getLastName()
    {
        return $this->surname;
    }

    /**
     * Sets last name to a given value.
     *
     * @param $name Last name value to set.
     *
     * @return static Instance of self with modified last name.
     */
    public function setLastName($name)
    {
        $this->surname = $name;
        return $this;
    }

    /**
     * Retrieves student full name (First and last name combined).
     *
     * @return string Student full name.
     */
    public function getFullName()
    {
        return "{$this->name} {$this->surname}";
    }

    /**
     * Retrieves student gender.
     *
     * @return string Student gender.
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets student gender to a given value.
     *
     * @param $gender Gender to set.
     *
     * @return static Instance of self with modified gender.
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Retrieves student group.
     *
     * @return string Student group.
     */
    public function getGroup()
    {
        return $this->sgroup;
    }

    /**
     * Sets group to a given value.
     *
     * @param $group Group to set.
     *
     * @return static Instance of self with modified group.
     */
    public function setGroup($group)
    {
        $this->sgroup = $group;
        return $this;
    }

    /**
     * Retrieves student email.
     *
     * @return string Student email.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets email to a given value.
     *
     * @param $email Email to set.
     *
     * @return static Instance of self with modified email.
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Retrieves student birth year.
     *
     * @return int Student birth year.
     */
    public function getBirthYear()
    {
        return $this->byear;
    }

    /**
     * Sets birth year to a given value.
     *
     * @param $year Birth year value to set.
     *
     * @return static Instance of self with modified birth year.
     */
    public function setBirthYear($year)
    {
        $this->byear = $year;
        return $this;
    }

    /**
     * Retrieves student status.
     *
     * @return string Student status.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets status to a given value.
     *
     * @param $status Status value to set.
     *
     * @return static Instance of self with modified status.
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Retrieves student rating.
     *
     * @return int Student rating.
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Sets rating to a given value.
     *
     * @param $rating Rating value to set.
     *
     * @return static Instance of self with modified rating.
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * Retrieves an auth token.
     *
     * @return string Auth token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets an auth token to a given value.
     *
     * @param $token Token value to set.
     *
     * @return static Instance of self with modified auth token.
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
}