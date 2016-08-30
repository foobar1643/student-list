<?php

namespace App\Helper;

use App\Entity\Student;
use App\Database\StudentDataGateway;

/**
 * Authorizes current user, gets a authorization token for current user,
 *  determines if current user is authorized or can manage a given file.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 */
class AuthHelper
{
    /** @var DataGateway $dataGateway StudentDataGateway instance. */
    private $dataGateway;
    /** @var Student $authorizedUser Authorized user object. */
    private $authorizedUser;

    /**
     * Constructor.
     *
     * @param StudentDataGateway $dataGateway StudentDataGateway instance.
     */
    public function __construct(StudentDataGateway $dataGateway)
    {
        $this->dataGateway = $dataGateway;
        $this->authorizedUser = null;
    }

    /**
     * Returns a student object if user authorized, false if user is not authorized.
     *
     * @return Student|bool
     */
    public function getAuthorizedUser()
    {
        if(isset($_COOKIE['auth']) && $this->authorizedUser === null) {
            $this->authorizedUser = $this->dataGateway->selectStudent($_COOKIE['auth']);
        }
        return ($this->authorizedUser === null) ? false : $this->authorizedUser;
    }

    /**
     * Authorizes given student.
     *
     * @param Student $student A student to authorize.
     *
     * @return bool
     */
    public function authorizeStudent(Student $student)
    {
        return setcookie("auth", $student->getToken(), time()+36000000);
    }

    /**
     * Returns randomly generated authorization token.
     *
     * @return string
     */
    public function createAuthToken() {
        return TokenGenerator::generateToken(45);
    }

    /**
     * Removes an authorization cookie from user.
     *
     * @return void
     */
    public function logOut() {
        setcookie('auth', "", 0);
    }
}