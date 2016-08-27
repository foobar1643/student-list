<?php

namespace App\Helper;

class TableHelper {

    private $getOptions;

    public $page;
    public $searchPattern;
    public $sortKey;
    public $sortType;

    private function getParam($option) {
        return isset($this->getOptions[$option]) ? $this->getOptions[$option] : null;
    }

    private function getHttpQueryArray() {
        return ['page' => $this->page,
                    'sort' => $this->sortKey,
                    'type' => $this->sortType,
                    'search' => $this->searchPattern];
    }

    public function __construct(array $getOptions) {
        $this->getOptions = $getOptions;

        $this->page = $this->getParam('page');
        $this->searchPattern = $this->getParam('search');
        $this->sortKey = $this->getParam('sort');
        $this->sortType = $this->getParam('type');
    }

    public function getSortingLink($element) {
        $httpQuery = $this->getHttpQueryArray();
        $httpQuery['sort'] = $element;
        $httpQuery['type'] = $this->sortType == 'asc' ? 'desc' : 'asc';
        return sprintf('?%s', http_build_query($httpQuery));
    }

    public function getPageLink($page) {
        $httpQuery = $this->getHttpQueryArray();
        $httpQuery['page'] = $page;
        return sprintf('?%s', http_build_query($httpQuery));
    }

    public function getSortingGlyphicon() {
        return $this->sortType == 'desc' ? 'glyphicon-arrow-down' : 'glyphicon-arrow-up';
    }

    public function getTableHeaders() {
        return ["name" => "Имя", "surname" => "Фамилия",
            "sgroup" => "Группа", "rating" => "Баллы"];
    }
}