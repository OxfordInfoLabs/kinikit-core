<?php


namespace Kinikit\Core\Binding;


class ComplexObject {

    /**
     * Simple object
     *
     * @var SimpleGetterSetterObj
     */
    private $simpleObject;

    /**
     * @var string
     */
    private $title;


    /**
     * Other object
     *
     * @var SimpleConstructorObject[string][]
     */
    private $otherObjs;


    /**
     * @var string[]
     */
    private $games;


    /**
     * ComplexObject constructor.
     * @param SimpleGetterSetterObj $simpleObject
     */
    public function __construct(SimpleGetterSetterObj $simpleObject) {
        $this->simpleObject = $simpleObject;
    }

    /**
     * @return SimpleGetterSetterObj
     */
    public function getSimpleObject(): SimpleGetterSetterObj {
        return $this->simpleObject;
    }


    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void {
        $this->title = $title;
    }


    /**
     * Set a string array of games
     *
     * @param string[] $games
     */
    public function setGames($games) {
        $this->games = $games;
    }

    /**
     * @return string[]
     */
    public function getGames() {
        return $this->games;
    }


    /**
     * @return SimpleConstructorObject[string][]
     */
    public function getOtherObjs() {
        return $this->otherObjs;
    }

    /**
     * @param SimpleConstructorObject[string][] $otherObjs
     */
    public function setOtherObjs($otherObjs) {
        $this->otherObjs = $otherObjs;
    }


}
