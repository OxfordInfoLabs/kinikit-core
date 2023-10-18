<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 24/09/2018
 * Time: 17:52
 */

namespace Kinikit\Core\Util;


class NestedClass  {

    private $id;
    private $description;


    /**
     * @var Kinikit\Core\Util\NestedClass[]
     */
    private $items;

    /**
     * NestedClass constructor.
     * @param $id
     * @param $description
     * @param Kinikit\Core\Util\NestedClass[] $items
     */
    public function __construct($id = null, $description = null, $items = null) {
        $this->id = $id;
        $this->description = $description;
        $this->items = $items;
    }


    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items) {
        $this->items = $items;
    }


}