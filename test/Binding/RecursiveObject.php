<?php


namespace Kinikit\Core\Binding;


class RecursiveObject {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var RecursiveObject
     */
    private $subObject;


    /**
     * RecursiveObject constructor.
     */
    public function __construct($id, $subObject = null) {
        $this->id = $id;
        $this->subObject = $subObject;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return RecursiveObject
     */
    public function getSubObject() {
        return $this->subObject;
    }

    /**
     * @param RecursiveObject $subObject
     */
    public function setSubObject($subObject) {
        $this->subObject = $subObject;
    }


}
