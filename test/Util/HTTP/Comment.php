<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 10/10/2018
 * Time: 11:17
 */

namespace Kinikit\Core\Util\HTTP;


use Kinikit\Core\Object\SerialisableObject;

class Comment extends SerialisableObject {

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
     * @return null
     */
    public function getPostId() {
        return $this->postId;
    }

    /**
     * @param null $postId
     */
    public function setPostId($postId) {
        $this->postId = $postId;
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
    public function getName() {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param null $email
     */
    public function setEmail($email) {
        $this->email = $email;
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