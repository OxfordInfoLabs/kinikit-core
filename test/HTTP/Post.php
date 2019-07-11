<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 10/10/2018
 * Time: 11:06
 */

namespace Kinikit\Core\HTTP;


class Post  {

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
     * @return mixed
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
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
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body) {
        $this->body = $body;
    }


}
