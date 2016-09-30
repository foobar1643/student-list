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
use Students\Database\StudentDataGateway;
use Students\Entity\Student;

/**
 * Validates a student entity.
 *
 * This class is not complete, in fact, it's a temporary solution.
 * What I want to make in the future is described in the Validator.php class doc.
 *
 */
class StudentValidator extends Validator
{
    const STUDENT_MIN_RATING = 0;
    const STUDENT_MAX_RATING = 300;

    const STUDENT_MIN_BIRTHYEAR = 1900;
    const STUDENT_MAX_BIRTHYEAR = 2000;

    protected $dataGateway;

    public function __construct(StudentDataGateway $dataGateway)
    {
        $this->dataGateway = $dataGateway;
    }

    public function validateStudent(Student $student)
    {
        $errors = [];
        $errors['firstName'] = $this->validateName($student->getFirstName());
        $errors['lastName'] = $this->validateName($student->getLastName());
        $errors['gender'] = $this->validateGender($student->getGender());
        $errors['group'] = $this->validateGroup($student->getGroup());
        $errors['email'] = $this->confirmEmail($student->getEmail(), $student->getToken());
        $errors['birthYear'] = $this->validateBirthYear($student->getBirthYear());
        $errors['status'] = $this->validateStatus($student->getStatus());
        $errors['rating'] = $this->validateRating($student->getRating());
        return array_filter($errors, [$this, 'filterErrors']);
    }

    protected function confirmEmail($email, $token)
    {
        $validatedEmail = $this->validateEmail($email);
        if($validatedEmail !== true) {
            return $validatedEmail;
        }
        if($this->dataGateway->checkEmail($email, $token) !== false) {
            return 'This email is already taken by another user.';
        }
        return true;
    }

    protected function validateGender($gender)
    {
        if($gender !== Student::GENDER_MALE && $gender !== Student::GENDER_FEMALE) {
            return "Gender must be either male or female.";
        }
        return true;
    }

    protected function validateBirthYear($year)
    {
        if($year < self::STUDENT_MIN_BIRTHYEAR || $year > self::STUDENT_MAX_BIRTHYEAR) {
            return "Birth year must be bigger than " . self::STUDENT_MIN_BIRTHYEAR
                ." and lesser than " . self::STUDENT_MAX_BIRTHYEAR;
        }
        return true;
    }

    protected function validateStatus($status)
    {
        if($status !== Student::STATUS_RESIDENT && $status !== Student::STATUS_NONRESIDENT) {
            return "Status must be either resident or non-resident.";
        }
        return true;
    }

    protected function validateRating($rating)
    {
        if($rating < self::STUDENT_MIN_RATING || $rating > self::STUDENT_MAX_RATING) {
            return "Rating must be bigger than " . self::STUDENT_MIN_RATING
                ." and lesser than " . self::STUDENT_MAX_RATING;
        }
        return true;
    }
}