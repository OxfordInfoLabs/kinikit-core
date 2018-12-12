<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 10/10/2018
 * Time: 11:06
 */

namespace Kinikit\Core\Util\HTTP;


use Kinikit\Core\Object\SerialisableObject;

class Post extends SerialisableObject {

    private $userId;
    private $id;
    private $title;
    private $body;


    /**
     * Post constructor.
     * @param $userId
     * @param $id
     * @param $title
     * @param $body
     */
    public function __construct($userId = null, $id = null, $title = null, $body = null) {
        $this->userId = $userId;
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * @return null
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param null $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @return null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param null $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return null
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param null $body
     */
    public function setBody($body) {
        $this->body = $body;
    }


}