<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 13:04
 */

namespace Kinikit\Core\Annotation;

/**
 * @mapped
 * @ormTable active_record_container
 * @authors mark, nathan,lucien
 *
 * Class TestAnnotatedClass
 * @package Kinikit\Core\Util\Annotation
 */
class TestAnnotatedClass {

    /**
     * @field
     * @primaryKey
     * @ormColumn tag_name
     * @validation required
     */
    private $tag;

    /**
     * @field
     * @validation required,maxlength(255)
     */
    private $description;

    /**
     * @relationship
     * @multiple
     * @relatedClass TestActiveRecord
     * @relatedFields tag=>containerTag
     * @orderingFields id DESC
     */
    private $activeRecords;

    private $nonPersisted;


    /**
     * @return mixed
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag) {
        $this->tag = $tag;
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
    public function getActiveRecords() {
        return $this->activeRecords;
    }

    /**
     * @param mixed $activeRecords
     */
    public function setActiveRecords($activeRecords) {
        $this->activeRecords = $activeRecords;
    }

    /**
     * @return mixed
     */
    public function getNonPersisted() {
        return $this->nonPersisted;
    }

    /**
     * @param mixed $nonPersisted
     */
    public function setNonPersisted($nonPersisted) {
        $this->nonPersisted = $nonPersisted;
    }

}
