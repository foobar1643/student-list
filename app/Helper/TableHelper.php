<?php

namespace App\Helper;

class TableHelper {

    public $pageNumber;
    public $searchPattern;
    public $sortKey;
    public $sortType;

    public function getLinkForSorting($key) {
        $sortType = $this->sortType;
        $data = array("sort" => $key);
        if($sortType == "desc") {
            $sortType = "asc";
        } else {
            $sortType = "desc";
        }
        $data["type"] = $sortType;
        if(isset($this->pageNumber)) {
            $data["page"] = $this->pageNumber;
        }
        if(isset($this->searchPattern)) {
            $data["search"] = $this->searchPattern;
        }
        return "?" . http_build_query($data);
    }

    public function getLinkForPage($page) {
        $data = array("page" => $page);
        if(isset($this->sortKey) && isset($this->sortType)) {
            $data["sort"] = $this->sortKey;
            $data["type"] = $this->sortType;
        }
        if(isset($this->searchPattern)) {
            $data["search"] = $this->searchPattern;
        }
        return "?" . http_build_query($data);
    }

    public function getGlyphicon() {
        if($this->sortType == 'asc') {
            return "glyphicon-arrow-up";
        }
        return "glyphicon-arrow-down";
    }

    public function getTableHeader() {
        return ["name" => "Имя", "surname" => "Фамилия",
            "sgroup" => "Группа", "rating" => "Баллы"];
    }
}