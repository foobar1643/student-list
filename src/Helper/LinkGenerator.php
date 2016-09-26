<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Helper
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Helper;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Generates links for templates.
 */
class LinkGenerator
{

    /**
     * Request URI.
     * @var \Psr\Http\Message\UriInterface
     */
    protected $uri;

    /**
     * Page number for current request.
     * @var int|null
     */
    protected $page;

    /**
     * Sorting key for current request.
     * @var string|null
     */
    protected $sortingKey;

    /**
     * Sorting type for current request.
     * @var string|null
     */
    protected $sortingType;

    /**
     * Search query for current request.
     * @var string|null
     */
    protected $searchQuery;

    /**
     * Counstrictor
     *
     * @param ServerRequestInterface $request Request instance.
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->uri = $request->getUri();
        $this->page = $request->getQueryParam('page');
        $this->sortingKey = $request->getQueryParam('key');
        $this->sortingType = $request->getQueryParam('type');
        $this->searchQuery = $request->getQueryParam('search');
    }

    /**
     * Retrieves an array with link parameters.
     *
     * @return array Associative array with link parameters.
     */
    protected function getHttpQueryArray()
    {
        return [
            'page' => $this->page,
            'key' => $this->sortingKey,
            'type' => $this->sortingType,
            'search' => $this->searchQuery];
    }

    /**
     * Forms an absolute link using the URI object from current reuqest.
     *
     * @param string $query Query string to use.
     *
     * @return string Absolute URI with given query.
     */
    protected function formLink($query)
    {
        $uri = $this->uri->withQuery($query);
        return $uri->__toString();
    }

    /**
     * Retrieves an absolute link to a given page.
     *
     * @param int $page Page number to create link to.
     *
     * @return string Absolute link to a given page.
     */
    public function toPage($page)
    {
        $query = $this->getHttpQueryArray();
        $query['page'] = $page;
        return $this->formLink(http_build_query($query));
    }

    /**
     * Retrieves a link with sotring pattern and type.
     *
     * @param StringUtils $key Sorting key.
     *
     * @return string Absoulte link to sorting with given key.
     */
    public function toSorting($key)
    {
        $query = $this->getHttpQueryArray();
        $query['key'] = $key;
        $query['type'] = $this->sortingType === 'asc' ? 'desc' : 'asc';
        return $this->formLink(http_build_query($query));
    }
}