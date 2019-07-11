<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 10/10/2018
 * Time: 11:17
 */

namespace Kinikit\Core\HTTP;


class Comment  {

    private $postId;
    private $id;
    private $name;
    private $email;
    private $body;

    /**
     * Comment constructor.
     * @param $postId
     * @param $id
     * @param $name
     * @param $email
     * @param $body
     */
    public function __construct($postId = null, $id = null, $name = null, $email = null, $body = null) {
        $this->postId = $postId;
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getPostId() {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId) {
        $this->postId = $postId;
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
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
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
