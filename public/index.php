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
        $request->getQueryParam('key', 'id'),
        $request->getQueryParam('type', 'asc'));

    return $container['view']->renderTemplate('index.phtml', $response, [
        'linker' => new LinkGenerator($request),
        'students' => $students,
        'pager' => $pager,
        'page' => $page
    ]);
});

$app->route('/form', 'GET', function(Request $request, Response $response) use($container) {
    return $container['view']->renderTemplate('form.phtml', $response, []);
});

$app->start();