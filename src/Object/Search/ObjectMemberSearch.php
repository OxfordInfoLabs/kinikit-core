<?php

namespace Kinikit\Core\Object\Search;

use Kinikit\Core\Object\SerialisableObject;

class ObjectMemberSearch extends SerialisableObject {

    protected $filters = array();
    protected $member;

    public function __construct($filters = array(), $member = null) {
        $this->filters = $filters;
        $this->member = $member;
    }


    /**
     * @return array
     */
    public function getFilters() {
        return $this->filters;
    }


    /**
     * @return mixed
     */
    public function getMember() {
        return $this->member;
    }


}