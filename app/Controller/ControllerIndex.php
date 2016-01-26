<?php

namespace App\Controller;
use \App\Model\Student;
use \App\Database\StudentDataGateway;
use \App\Config;
use \App\Bootstrap as Bootstrap;
use \App\Model\Helper\LinkHelper;
use \App\Model\Helper\PaginationHelper;

class ControllerIndex {

    public function run() {
        $viewSettings = array();
        $linkBuilder = new LinkHelper();
        $config = new Config();
        $dataGateway = new StudentDataGateway(Bootstrap::getPDO());
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
                $linkBuilder->is_searching = true;
                $linkBuilder->search_pattern = $_GET['search'];
                $searchPattern = $_GET['search'];
            }
        }
        $studentsCount = $dataGateway->get_total_students($searchPattern);
        $pager = new PaginationHelper($studentsCount, $config->getKey('pagination', 'elemPerPage'), "index.php?page=");
        $viewSettings["totalPages"] = $pager->get_total_pages();
        if($viewSettings['currentPage'] > $viewSettings['totalPages']) $viewSettings['currentPage'] = 1;
        $linkBuilder->sort_key = $currentPattern;
        $linkBuilder->sort_type = $sortingType;
        $offset = $pager->get_offset_for_page($viewSettings["currentPage"]);
        $viewSettings["nextPage"] = $pager->get_link_to_page($viewSettings["currentPage"] + 1); // get link to the next page
        $viewSettings["previousPage"] = $pager->get_link_to_page($viewSettings["currentPage"] - 1); // get link to the previous page
        $viewSettings["students"] = $dataGateway->select_students($offset, $currentPattern, $sortingType, $searchPattern);
        $viewSettings["pageTitle"] = "Главная страница";
        $viewSettings["navTitle"] = "index";
        for($i = 0; $i < count($viewSettings['students']); $i++) {
            foreach(array_keys(get_object_vars($viewSettings["students"][$i])) as $key) {
                $viewSettings["students"][$i]->$key = htmlspecialchars($viewSettings["students"][$i]->$key);
            }
        }
        include_once("../templates/header.html");
        include_once("../templates/index.html");
    }
}