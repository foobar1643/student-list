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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Students\Utility\StringUtils;
use Students\Exception\BadRequestException;

/**
 * Provides basic protection against CSRF exploit.
 */
class CSRFProtection
{
    /**
     * CSRF token.
     * @var string
     */
    protected $csrfToken;

    /**
     * Constructor. Randomly generates new CSRF token every time this method is called.
     */
    public function __construct()
    {
        $this->csrfToken = StringUtils::generate(50);
    }

    /**
     * Retrieves a CSRF token value.
     *
     * @return string CSRF token.
     */
    public function getCsrfToken()
    {
        return $this->csrfToken;
    }

    /**
     * Adds a CSRF cookie to given PSR-7 Response instance.
     *
     * @param ResponseInterface $response Response instance with CSRF cookie.
     */
    public function setResposneCookie(ResponseInterface $response)
    {
        $dateTime = new \DateTime("now");
        $dateTime->add(new \DateInterval("P1D"));
        return FigResponseCookies::set($response, SetCookie::create('csrf')
                    ->withValue($this->csrfToken)
                    ->withExpires($dateTime->format(\DateTime::COOKIE))
                    ->withPath('/'));
    }

    /**
     * Validates CSRF token in given PSR-7 Request instance.
     *
     * @param ServerRequestInterface $request PSR-7 Request instance.
     *
     * @throws \Students\Exception\BadRequestException If CSRF check failed.
     *
     * @return boolean True if CSRF check was successful.
     */
    public function validateCsrfToken(ServerRequestInterface $request)
    {
        $formToken = isset($request->getParsedBody()['csrf']) ? strval($request->getParsedBody()['csrf']) : '';
        $cookie = FigRequestCookies::get($request, 'csrf');
        if($cookie->getValue() !== $formToken) {
            throw new BadRequestException($request);
        }
        return true;
    }
}