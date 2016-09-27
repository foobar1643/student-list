<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Helper
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Helper;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\SetCookie;
use Students\Database\StudentDataGateway;
use Students\Entity\Student;
use Students\Utility\StringUtils;

/**
 * Manages authorization for students.
 *
 * @todo Look up the naming. Authentication is not Authorization.
 *
 * This class manages students authorization using cookies and PSR-7 Request
 * and Response instances. To authorize a user, it sends an authorization
 * cookie to the client. Authorization cookie contains an authorization token,
 * which is unique for every student entity.
 *
 * This class also deauthorizes a user, checks if user in given request instance
 * is authorized, retrieves an auth token if a user has an authorization
 * cookie, generates an auth token for a given student entity.
 *
 */
class StudentAuthorization
{
    /**
     * Returns true if user in given request instance is authorized, returns false
     * otherwise.
     *
     * @param ServerRequestInterface $request Request instance to check.
     *
     * @return boolean True if user is authorized, false otherwise.
     */
    public function isAuthorized(ServerRequestInterface $request)
    {
        $cookie = FigRequestCookies::get($request, 'authorization');
        return empty($cookie->getValue()) ? false : true;
    }

    /**
     * If request instance has authorization cookie, returns the value of the cookie.
     * If request instance does not have authorization cookie, returns a generated token.
     *
     * Right now getToken() function does two things:
     * If auth cookie exists, it returns its value.
     * If auth cookie does not exists, it generates a new token.
     * This is not compliant with SOLIDs SRP principle and probably should
     * be changed into something more flexible. But in that case, the code using
     * the funcgion will be longer. Should I do it?
     *
     * @param ServerRequestInterface $request Request instance to check for auth cookie.
     *
     * @return string Authorization token.
     */
    public function getToken(ServerRequestInterface $request)
    {
        return ($this->isAuthorized($request)) ? $this->getAuthToken($request)
            : StringUtils::generate(65);
    }

    /**
     * Returns an auth token if user in given request instance is authorized.
     * Returns null otherwise.
     *
     * @param ServerRequestInterface $request [description]
     *
     * @return string|null
     */
    public function getAuthToken(ServerRequestInterface $request)
    {
        $cookie = FigRequestCookies::get($request, 'authorization');
        return $cookie->getValue();
    }

    /**
     * Adds an auth token to a given Student instance.
     *
     * Warning: This method wil overwrite the existing auth token.
     *
     * @param Student $student Student entity with an auth token.
     */
    public function createAuthToken(Student $student)
    {
        return $student->setToken(StringUtils::generate(65));
    }

    /**
     * Authorizes given Student entity.
     *
     * This method authorizes user using cookies. In details, it adds an
     * authorization cookie to the given response instance. Authorization
     * cookie contains an authorization token, which is unique for every student
     * entity.
     *
     * This method throws an InvalidArgumentException if given student entity does
     * not have an authorization token. You can use setAuthToken() method to generate
     * an auth token for a student entity.
     *
     * @param Student $student Student to authorize
     * @param ResponseInterface $response Response instance for authorization cookie.
     *
     * @throws \InvalidArgumentException If given Student instance does not have an
     * authorization token.
     *
     * @return \Psr\Http\Message\ResponseInterface Response instance with authorization cookie.
     */
    public function authorizeUser(Student $student, ResponseInterface $response)
    {
        if(empty($student->getToken())) {
            throw new \InvalidArgumentException('Student must have an authorization'
            .' token in order to complete authorization.');
        }
        $dateTime = new \DateTime("now");
        $dateTime->add(new \DateInterval("P90D"));
        return FigResponseCookies::set($response, SetCookie::create('authorization')
            ->withValue($student->getToken())
            ->withExpires($dateTime->format(\DateTime::COOKIE))
            ->withPath('/'));
    }

    /**
     * Removes authorization cookie, deauthorizing the user.
     *
     * @param ResponseInterface $response Response instance.
     * Used to unset the authorization cookei.
     *
     * @return \Psr\Http\Message\ResponseInterface Response instance with
     * authorization cookie removed.
     */
    public function deauthorizeUser(ResponseInterface $response)
    {
        return FigResponseCookies::remove($response, 'authorization');
    }
}