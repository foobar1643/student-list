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
        $tableHelper = $this->container["tableHelper"];
        $config = $this->container["config"];
        $dataGateway = $this->container["dataGateway"];
        $searchPattern = null;
        $currentPage = 1;
        $sortingPatterns = array("name", "surname", "sgroup", "rating");
        $sortingType = "desc";
        $currentPattern = $sortingPatterns[3];
        if(isset($_GET['page'])) {
            $currentPage = intval($_GET['page']);
            $tableHelper->pageNumber = intval($_GET['page']);
        }
        if(isset($_GET['sort']) && isset($_GET['type']) && in_array($_GET['sort'], $sortingPatterns)) {
            $currentPattern = $_GET['sort'];
            $sortingType = $_GET['type'];
        }
        if(isset($_GET['search'])) {
            $searchQuery = trim($_GET['search']);
            if($searchQuery != "") {
                $tableHelper->searchPattern = $searchQuery;
                $searchPattern = $searchQuery;
            } else {
                $error = true;
            }
        }
        if(isset($_GET['notify']) && $_GET['notify'] == 'success' && $error == false) {
            $success = true;
        }
        $studentsCount = $dataGateway->getTotalStudents($searchPattern);
        $pager = new PaginationHelper($studentsCount, $config->getValue('pager', 'elemPerPage'));
        $currentPage = $pager->checkPage($currentPage);
        $tableHelper->sortKey = $currentPattern;
        $tableHelper->sortType = $sortingType;
        $students = $dataGateway->selectStudents($pager->getOffset($currentPage), $currentPattern, $sortingType,
            $searchPattern, $config->getValue('pager', 'elemPerPage'));
        include("../templates/index.html");
    }
}