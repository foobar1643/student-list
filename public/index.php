<?php

require(__DIR__ . '/../src/Bootstrap.php');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Students\Application;
use Students\Entity\Student;
use Students\Helper\Pagination;
use Students\Helper\LinkGenerator;
use Students\Utility\StringUtils;
use Students\Exception\ApplicationException;

$app = new Application();

// Map routes
$app->route('/', 'GET', function(Request $request, Response $response) use($container) {
    $searchQuery = $request->getQueryParam('search', '');

    $pager = new Pagination($container['studentGateway']->getTotalStudents($searchQuery), 15);
    $page = $request->getQueryParam('page', 1);
    $page = $pager->validatePageNumber($page);

    $students = $container['studentGateway']->searchStudents($searchQuery,
        $pager->getOffset($page), $pager->getLimit(),
        $request->filterQueryParam('key', ['name', 'surname', 'sgroup', 'rating'], 'rating'),
        $request->filterQueryParam('type', ['asc', 'desc'], 'desc'));

    return $container['view']->renderTemplate('index.phtml', $response, [
        'linker' => new LinkGenerator($request),
        'students' => $students,
        'pager' => $pager,
        'page' => $page,
        'authorized' => $container['studentAuthorization']->isAuthorized($request),
        'student' => $container['studentGateway']->selectStudent($container['studentAuthorization']->getAuthToken($request)),
        'notification' => $request->filterQueryParam('notification', ['added', 'edited']),
        'searchQuery' => $searchQuery
    ]);
});

$app->route('/form', ['GET', 'POST'], function(Request $request, Response $response) use($container) {
    $auth = $container['studentAuthorization'];
    $gateway = $container['studentGateway'];
    $csrfProtection = $container['csrfProtection'];

    $response = $csrfProtection->setResposneCookie($response);
    $student = !empty($auth->getAuthToken($request)) ?
        $gateway->selectStudent($auth->getAuthToken($request)) : new Student();

    if($request->getMethod() === 'POST') {
        $csrfProtection->validateCsrfToken($request);

        $student = Student::fromPostRequest($request);
        $student->setToken($auth->getToken($request));
        $errors = $container['studentValidator']->validateStudent($student);
        if(empty($errors)) {
            ($auth->isAuthorized($request)) ? $gateway->updateStudent($student) : $gateway->addStudent($student);
            $response = $auth->authorizeUser($student, $response);
            return $response->withHeader('Location', '/' . "?notification=" . ($auth->isAuthorized($request) ? 'edited' : 'added'));
        }
    }
# . $auth->isAuthorized($request) ? 'edited' : 'added'
    return $container['view']->renderTemplate('form.phtml', $response, [
        'student' => $student,
        'errors' => isset($errors) ? $errors : [],
        'csrfToken' => $csrfProtection->getCsrfToken(),
        'authorized' => $container['studentAuthorization']->isAuthorized($request)
    ]);
});

$app->start();