<?php

namespace App\Controller;

use Pimple\Container;
use \App\Model\Student;
use \App\Helper\PaginationHelper;

class ControllerIndex implements AppController {

    private $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $pageTitle = "Главная страница";
        $navTitle = "index";
        $error = false;
        $success = false;
        $isAuthorized = $this->container["authHelper"]->isAuthorized();
        $tableHelper = $this->container["tableHelper"];
        $config = $this->container["config"];
        $dataGateway = $this->container["dataGateway"];
        $tableHelper->searchPattern = null;
        $currentPage = 1;
        $sortingPatterns = array("name", "surname", "sgroup", "rating");
        $tableHelper->sortType = "desc";
        $tableHelper->sortKey = $sortingPatterns[3];
        if(isset($_GET['page'])) {
            $currentPage = intval($_GET['page']);
            $tableHelper->pageNumber = intval($_GET['page']);
        }
        if(isset($_GET['sort']) && isset($_GET['type']) && in_array($_GET['sort'], $sortingPatterns)) {
            $tableHelper->sortKey = $_GET['sort'];
            $tableHelper->sortType = $_GET['type'];
        }
        if(isset($_GET['search'])) {
            $searchQuery = trim($_GET['search']);
            if($searchQuery != "") {
                $tableHelper->searchPattern = $searchQuery;
            } else {
                $error = true;
            }
        }
        if(isset($_GET['notify']) && $_GET['notify'] == 'success' && $error == false) {
            $success = true;
        }
        $studentsCount = $dataGateway->getTotalStudents($tableHelper->searchPattern);
        $pager = new PaginationHelper($studentsCount, $config->getValue('pager', 'elemPerPage'));
        $currentPage = $pager->checkPage($currentPage);
        $students = $dataGateway->selectStudents($pager->getOffset($currentPage), $tableHelper->sortKey, $tableHelper->sortType,
            $tableHelper->searchPattern, $config->getValue('pager', 'elemPerPage'));
        include("../templates/index.html");
    }
}