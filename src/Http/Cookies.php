<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Http
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

/**
 * Cookies concept:
 *
 * This class stores HTTP message cookies. It extends standart application collection
 * (\Students\Utility\Collection). A cookie is represented by the Cookie object (\Students\Http\Cookie), which
 * encapsulates cookie values (name, value, expiration date, path, domain, httpOnly).
 * I can integrate this class into Request and Response implementations, so I don't have
 * to use external libraries like FIGCookies. The end result should work like every other
 * collection of data in PSR-7 HTTP messages. Examples:
 * $response->withCookie('name', 'value', 'P1D', '/', 'localhost'); // Returns Response instance with added cookie
 * $response->withoutCookie('name'); // Returns Response instance without cookie
 * $request->getCookie('name'); // Returns a Cookie object
 * $request->getCookieValue('name'); // Returns a cookie value
 * Obivously, this solution won't use $_COOKIES superglobal. Modified\added\removed
 * cookies would be rendered into HTTP headers (e.g. Set-Cookie) directly.
 *
 * The good side of this method is that I don't have to parse HTTP headers every time
 * I want to get a specific cookie value. Cookie headers would be parsed in the __construct()
 * method of the Request object, and parsed results would be encapsulated within this collection.
 * I also want to note that this collection is meant only to store cookies. Modification of the
 * HTTP message headers would be a separate task. However, to make it easy, this collection
 * would implement something like renderIntoCookieHeader() method. Obivously from the name,
 * the method would render stored cookies into HTTP headers.
 *
 * The bad side is that I think I'm overdoing the storage. I'm already storing cookie data
 * in the HTTP headers, and adding another collection would only overcomplicate the code.
 *
 * I should also think about limitations this method of dealing with cookies would create.
 */