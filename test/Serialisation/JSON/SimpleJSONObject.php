<?php

namespace Kinikit\Core\Serialisation\JSON;
use Kinikit\Core\Object\SerialisableObject;


/**
 * Simple JSON Object
 *
 */
class SimpleJSONObject extends SerialisableObject {

    private $myMember;
    private $yourMember;
    private $ourMember;

    /**
     * Create a simple JSON Object
     *
     * @param mixed $myMember
     * @param mixed $yourMember
     * @param mixed $ourMember
     * @return SimpleJSONObject
     */
    public function __construct($myMember = null, $yourMember = null, $ourMember = null) {
        $this->myMember = $myMember;
        $this->yourMember = $yourMember;
        $this->ourMember = $ourMember;
    }

    /**
     * @return unknown
     */
    public function getMyMember() {
        return $this->myMember;
    }

    /**
     * @return unknown
     */
    public function getOurMember() {
        return $this->ourMember;
    }

    /**
     * @return unknown
     */
    public function getYourMember() {
        return $this->yourMember;
    }

    /**
     * @param unknown_type $myMember
     */
    public function setMyMember($myMember) {
        $this->myMember = $myMember;
    }

    /**
     * @param unknown_type $ourMember
     */
    public function setOurMember($ourMember) {
        $this->ourMember = $ourMember;
    }

    /**
     * @param unknown_type $yourMember
     */
    public function setYourMember($yourMember) {
        $this->yourMember = $yourMember;
    }

}

?>
