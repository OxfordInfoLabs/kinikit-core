<?php

namespace Kinikit\Core\Object\Search;

use Kinikit\Core\Object\SerialisableObject;

class ObjectSearch extends SerialisableObject {

    /**
     * Key / Value array of member / value filters for limiting an object search.
     *
     * @var string[string]
     */
    protected $filters = array();

    /**
     * Array of orderings in the format e.g. member ASC etc.
     *
     * @var string[]
     */
    protected $orderings = array();

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var int
     */
    protected $page;


    /**
     * Construct with all members
     *
     * ObjectSearch constructor.
     * @param array $filters
     * @param array $orderings
     * @param int $pageSize
     * @param int $page
     */
    public function __construct($filters = array(), $orderings = array(), $pageSize = 10, $page = 1) {
        $this->filters = $filters;
        $this->orderings = $orderings;
        $this->pageSize = $pageSize;
        $this->page = $page;
    }


    /**
     * Get the filters in use for this search
     *
     * @return array
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * Get the orderings in use
     *
     * @return array
     */
    public function getOrderings() {
        return $this->orderings;
    }

    /**
     * Get the page size
     *
     * @return mixed
     *
     */
    public function getPageSize() {
        return $this->pageSize;
    }

    /**
     * Get page
     *
     * @return mixed
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters) {
        $this->filters = $filters;
    }

    /**
     * @param array $orderings
     */
    public function setOrderings($orderings) {
        $this->orderings = $orderings;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize($pageSize) {
        $this->pageSize = $pageSize;
    }

    /**
     * @param int $page
     */
    public function setPage($page) {
        $this->page = $page;
    }


}