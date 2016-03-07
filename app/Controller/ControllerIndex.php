<?php

namespace App\Controller;

use Pimple\Container;
use \App\Model\Student;
use \App\Helper\TableLinkHelper;
use \App\Helper\PaginationHelper;

class ControllerIndex {

    private $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $pageTitle = "Главная страница";
        $navTitle = "index";
        $error = false;
        $linkBuilder = new TableLinkHelper();
        $config = $this->container["config"];
        $dataGateway = $this->container["dataGateway"];
        $searchPattern = null;
        $currentPage = 1;
        $sortingPatterns = array("name", "surname", "sgroup", "rating");
        $sortingType = "desc";
        $currentPattern = $sortingPatterns[3];
        if(isset($_GET['page'])) {
            $currentPage = intval($_GET['page']);
            $linkBuilder->pageNumber = intval($_GET['page']);
        }
        if(isset($_GET['sort']) && isset($_GET['type']) && in_array($_GET['sort'], $sortingPatterns)) {
            $currentPattern = $_GET['sort'];
            $sortingType = $_GET['type'];
        }
        if(isset($_GET['search'])) {
            $searchQuery = trim($_GET['search']);
            if($searchQuery != "") {
                $linkBuilder->searchPattern = $searchQuery;
                $searchPattern = $searchQuery;
            } else {
                $error = true;
            }
        }
        $studentsCount = $dataGateway->getTotalStudents($searchPattern);
        $pager = new PaginationHelper($studentsCount, $config->getValue('pager', 'elemPerPage'));
        $currentPage = $pager->checkPage($currentPage);
        $linkBuilder->sortKey = $currentPattern;
        $linkBuilder->sortType = $sortingType;
        $students = $dataGateway->selectStudents($pager->getOffset($currentPage), $currentPattern, $sortingType,
            $searchPattern, $config->getValue('pager', 'elemPerPage'));
        include("../templates/index.html");
    }
}