<?php

namespace App\Controller;

use Pimple\Container;
use App\Helper\PaginationHelper;
use App\Helper\TableHelper;

class ControllerIndex implements AppController
{
    private $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    public function __invoke()
    {
        $tableHelper = new TableHelper($_GET);
        $isAuthorized = $this->container["authHelper"]->getAuthorizedUser();
        $dataGateway = $this->container["dataGateway"];
        $pager = new PaginationHelper($dataGateway->getTotalStudents($this->getParam('search', null)), 15);
        $currentPage = $pager->checkPage($this->getParam('page', 1));
        $success = $this->getParam('notify', false);
        $students = $dataGateway->searchStudents($this->getParam('search', null), $pager->getOffset($currentPage),
            $pager->getLimit(), $this->getSortKey(), $this->getSortOrder());
        include('../templates/index.phtml');
    }

    private function getParam($paramName, $defaultValue)
    {
        return isset($_GET[$paramName]) ? $_GET[$paramName] : $defaultValue;
    }

    private function getSortKey()
    {
        $sortingKeys = ["name", "surname", "sgroup", "rating"];
        $key = isset($_GET['sort']) ? array_search($_GET['sort'], $sortingKeys) : false;
        return ($key === false) ? "name" : $sortingKeys[$key];
    }

    private function getSortOrder()
    {
        return (isset($_GET['type']) && $_GET['type'] === 'asc') ? $_GET['type'] : 'desc';
    }
}