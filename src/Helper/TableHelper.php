<?php

namespace App\Helper;

/**
 * A helper class for student table on the main page.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 */
class TableHelper {

    /** @var array $getOptions Max rating for student to enter. */
    private $getOptions;

    /** @var int $page Current page. */
    public $page;
    /** @var string $searchPattern Current search pattern. */
    public $searchPattern;
    /** @var string $sortKey Current sorting key. */
    public $sortKey;
    /** @var string $sortType Current sorting type. */
    public $sortType;

    /**
     * Constructor.
     *
     * @param StudentDataGateway $gateway StudentDataGateway instance.
     */
    public function __construct(array $getOptions) {
        $this->getOptions = $getOptions;

        $this->page = $this->getParam('page');
        $this->searchPattern = $this->getParam('search');
        $this->sortKey = $this->getParam('sort');
        $this->sortType = $this->getParam('type');
    }

    /**
     * Returns a sorting link for given element.
     *
     * @param string $element Sorting element.
     *
     * @return string
     */
    public function getSortingLink($element) {
        $httpQuery = $this->getHttpQueryArray();
        $httpQuery['sort'] = $element;
        $httpQuery['type'] = $this->sortType == 'asc' ? 'desc' : 'asc';
        return sprintf('?%s', http_build_query($httpQuery));
    }

    /**
     * Returns a link to a given page.
     *
     * @param int $page Page number.
     *
     * @return string
     */
    public function getPageLink($page) {
        $httpQuery = $this->getHttpQueryArray();
        $httpQuery['page'] = $page;
        return sprintf('?%s', http_build_query($httpQuery));
    }

    /**
     * Returns a glyphicon HTML class for current sorting type.
     *
     * @return string
     */
    public function getSortingGlyphicon() {
        return $this->sortType == 'desc' ? 'glyphicon-arrow-down' : 'glyphicon-arrow-up';
    }

    /**
     * Returns an array with table headers.
     *
     * @return array
     */
    public function getTableHeaders() {
        return ["name" => "Имя", "surname" => "Фамилия",
            "sgroup" => "Группа", "rating" => "Баллы"];
    }

    /**
     * Returns a get parameter with a given name, if parameter with given name does not exists returns null.
     *
     * @param string $option Parameter name.
     *
     * @return string|null
     */
    private function getParam($option) {
        return isset($this->getOptions[$option]) ? $this->getOptions[$option] : null;
    }

    /**
     * Returns an array that can be used to build an http query.
     *
     * @return array
     */
    private function getHttpQueryArray() {
        return ['page' => $this->page,
                    'sort' => $this->sortKey,
                    'type' => $this->sortType,
                    'search' => $this->searchPattern];
    }
}