<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 13:01
 */

namespace Kinikit\Core\Annotation;

class Annotation {

    private string $label;
    private ?string $value;

    /**
     * Annotation constructor.
     * @param string $label
     * @param string|null $value
     */
    public function __construct(string $label, ?string $value = null) {
        $this->label = $label;
        $this->value = $value;
    }


    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void {
        $this->value = $value;
    }


    /**
     * Return value as an array split by ","
     */
    public function getValues(): array {
        $exploded = explode(",", $this->getValue());
        $values = [];
        foreach ($exploded as $entry) {
            $values[] = trim($entry);
        }

        return $values;
    }


}
