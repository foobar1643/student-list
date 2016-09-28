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

class CSRFProtection
{
    protected $csrfToken;

    public function __construct()
    {
        $this->csrfToken = StringUtils::generate(50);
    }

    public function getCsrfToken()
    {
        return $this->csrfToken;
    }

    public function setResposneCookie(ResponseInterface $response)
    {
        $dateTime = new \DateTime("now");
        $dateTime->add(new \DateInterval("P1D"));
        return FigResponseCookies::set($response, SetCookie::create('csrf')
                    ->withValue($this->csrfToken)
                    ->withExpires($dateTime->format(\DateTime::COOKIE))
                    ->withPath('/'));
    }

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