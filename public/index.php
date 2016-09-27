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

$app = new Application();

// Map routes
$app->route('/', 'GET', function(Request $request, Response $response) use($container) {
    $searchQuery = $request->getQueryParam('search', '');

    $pager = new Pagination($container['studentGateway']->getTotalStudents($searchQuery), 15);
    $page = $request->getQueryParam('page', 1);
    $page = $pager->validatePageNumber($page);

    $students = $container['studentGateway']->searchStudents($searchQuery,
        $pager->getOffset($page), $pager->getLimit(),
        $request->filterQueryParam('key', ['name', 'surname', 'sgroup', 'rating'], 'id'),
        $request->filterQueryParam('type', ['asc', 'desc'], 'asc'));

    return $container['view']->renderTemplate('index.phtml', $response, [
        'linker' => new LinkGenerator($request),
        'students' => $students,
        'pager' => $pager,
        'page' => $page
    ]);
});

$app->route('/form', 'GET', function(Request $request, Response $response) use($container) {
    $auth = $container['studentAuthorization'];
    $token = $auth->getAuthToken($request);
    $student = !empty($token) ? $container['studentGateway']->selectStudent($token)
        : new Student();

    return $container['view']->renderTemplate('form.phtml', $response, [
        'student' => $student,
        'errors' => [],
        'authorized' => $container['studentAuthorization']->isAuthorized($request)
    ]);
});

$app->route('/form', 'POST', function(Request $request, Response $response) use($container) {
    $auth = $container['studentAuthorization'];
    $gateway = $container['studentGateway'];

    $student = Student::fromPostRequest($request);
    $student->setToken($auth->getToken($request));
    $errors = $container['studentValidator']->validateStudent($student);

    if(empty($errors)) {
        ($auth->isAuthorized($request)) ? $gateway->updateStudent($student) : $gateway->addStudent($student);
        $response = $auth->authorizeUser($student, $response);
        return $response->withHeader('Location', '/');
    }

    return $container['view']->renderTemplate('form.phtml', $response, [
        'student' => $student,
        'errors' => $errors,
        'authorized' => $auth->isAuthorized($request)
    ]);
});

$app->start();