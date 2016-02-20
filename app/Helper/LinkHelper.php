<?php

namespace App\Helper;

class LinkHelper {

    public $is_paging;
    public $page_number;
    public $is_searching;
    public $search_pattern;
    public $is_sorting;
    public $sort_key;
    public $sort_type;

    public function getLinkForSorting($key) {
        $sortType = $this->sort_type;
        $data = array("sort" => $key);
        if($sortType == "desc") $sortType = "asc";
        else $sortType = "desc";
        $data["type"] = $sortType;
        if($this->is_paging) {
            $data["page"] = $this->page_number;
        }
        return "?" . http_build_query($data);
    }

    public function getLinkForPage($page) {
        $data = array("page" => $page);
        if($this->is_sorting) {
            $data["sort"] = $this->sort_key;
            $data["type"] = $this->sort_type;
        }
        if($this->is_searching) {
            $data["search"] = $this->search_pattern;
        }
        return "?" . http_build_query($data);
    }
}