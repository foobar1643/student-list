<?php

namespace App\Controller;

use Pimple\Container;
use App\Helper\PaginationHelper;
use App\Helper\TableHelper;

/**
 * Callable, displays a student list.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 */
class ControllerIndex implements AppController
{
    /** @var Container $container DI container. */
    private $container;

    /**
     * Constructor.
     *
     * @param Container $c DI container.
     */
    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    /**
     * A method that allows to use this class as a callable.
     *
     * @return void
     */
    public function __invoke()
    {
        $tableHelper = new TableHelper($_GET);
        $isAuthorized = $this->container["authHelper"]->getAuthorizedUser();
        $dataGateway = $this->container["dataGateway"];
        $pager = new PaginationHelper($dataGateway->getTotalStudents($this->getParam('search', null)), 15);
        $currentPage = $pager->checkPage($this->getParam('page', 1));
        $success = $this->getParam('notify', false);
        $students = $dataGateway->searchStudents($this->getParam('search', null), $pager->getOffset($currentPage),
            $pager->getLimit(), $this->getSortKey(), $this->getSortOrder());
        include('../templates/index.phtml');
    }

    /**
     * Returns a get parameter, or a default value, if get parameter does not exists.
     *
     * @param string $paramName Parameter name.
     * @param mixed $defaultValue Value to return if parameter is missing.
     *
     * @return string|mixed
     */
    private function getParam($paramName, $defaultValue)
    {
        return isset($_GET[$paramName]) ? $_GET[$paramName] : $defaultValue;
    }

    /**
     * Returns current sorting key.
     *
     * @return string
     */
    private function getSortKey()
    {
        $sortingKeys = ["name", "surname", "sgroup", "rating"];
        $key = isset($_GET['sort']) ? array_search($_GET['sort'], $sortingKeys) : false;
        return ($key === false) ? "name" : $sortingKeys[$key];
    }

    /**
     * Returns current sorting order.
     *
     * @return string
     */
    private function getSortOrder()
    {
        return (isset($_GET['type']) && $_GET['type'] === 'asc') ? $_GET['type'] : 'desc';
    }
}