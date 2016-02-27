<?php

namespace App\Controller;

use Pimple\Container;
use \App\Model\Student;
use \App\Helper\LinkHelper;
use \App\Helper\PaginationHelper;

class ControllerIndex {

    private $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $linkBuilder = new LinkHelper();
        $config = $this->container["config"];
        $dataGateway = $this->container["dataGateway"];
        $searchPattern = null;
        $currentPage = 1;
        $sortingPatterns = array("name", "surname", "sgroup", "rating");
        $sortingType = "desc";
        $currentPattern = $sortingPatterns[3];
        if($_GET) {
            if(isset($_GET['page'])) {
                $currentPage = $_GET['page'];
                $linkBuilder->pageNumber = $_GET['page'];
            }
            if(isset($_GET['sort']) && isset($_GET['type']) && in_array($_GET['sort'], $sortingPatterns)) {
                $currentPattern = $_GET['sort'];
                $sortingType = $_GET['type'];
            }
            if(isset($_GET['search'])) {
                if(trim($_GET['search']) != "") {
                    $linkBuilder->searchPattern = $_GET['search'];
                    $searchPattern = $_GET['search'];
                } else {
                    $error = true;
                }
            }
        }
        $studentsCount = $dataGateway->get_total_students($searchPattern);
        $pager = new PaginationHelper($studentsCount, $config->getValue('pager', 'elemPerPage'));
        $currentPage = $pager->checkPage($currentPage);
        $linkBuilder->sortKey = $currentPattern;
        $linkBuilder->sortType = $sortingType;
        $students = $dataGateway->select_students($pager->getOffset($currentPage), $currentPattern, $sortingType,
            $searchPattern, $config->getValue('pager', 'elemPerPage'));
        $pageTitle = "Главная страница";
        $navTitle = "index";
        include("../templates/index.html");
    }
}