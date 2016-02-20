<?php

namespace App\Controller;
use \App\Model\Student;
use \App\Helper\LinkHelper;
use \App\Helper\PaginationHelper;
use \App\Container;

class ControllerIndex {

    protected $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $viewSettings = array();
        $linkBuilder = new LinkHelper();
        $config = $this->container->getConfig();
        $dataGateway = $this->container->getDataGateway();
        $searchPattern = null;
        $viewSettings["currentPage"] = 1;
        $sortingPatterns = array("name", "surname", "sgroup", "rating");
        $sortingType = "desc";
        $currentPattern = $sortingPatterns[3];
        if($_GET) {
            if(isset($_GET['page'])) {
                $viewSettings['currentPage'] = $_GET['page'];
                $linkBuilder->is_paging = true;
                $linkBuilder->page_number = $_GET['page'];
            }
            if(isset($_GET['sort']) && isset($_GET['type']) && in_array($_GET['sort'], $sortingPatterns)) {
                $linkBuilder->is_sorting = true;
                $currentPattern = $_GET['sort'];
                $sortingType = $_GET['type'];
            }
            if(isset($_GET['search'])) {
                if(trim($_GET['search']) != "") {
                    $linkBuilder->is_searching = true;
                    $linkBuilder->search_pattern = $_GET['search'];
                    $searchPattern = $_GET['search'];
                } else {
                    $viewSettings['error'] = true;
                }
            }
        }
        $studentsCount = $dataGateway->get_total_students($searchPattern);
        $pager = new PaginationHelper($studentsCount, $config->getValue('pager', 'elemPerPage'), "index.php?page=");
        $viewSettings["pager"] = $pager;
        if($viewSettings['currentPage'] >  $pager->get_total_pages()) $viewSettings['currentPage'] = 1;
        $linkBuilder->sort_key = $currentPattern;
        $linkBuilder->sort_type = $sortingType;
        $viewSettings["students"] = $dataGateway->select_students($pager->get_offset_for_page($viewSettings["currentPage"]), $currentPattern, $sortingType,
            $searchPattern, $config->getValue('pager', 'elemPerPage'));
        $viewSettings["pageTitle"] = "Главная страница";
        $viewSettings["navTitle"] = "index";
        include("../templates/index.html");
    }
}