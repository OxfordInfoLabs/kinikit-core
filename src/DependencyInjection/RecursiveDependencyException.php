<?php


namespace Kinikit\Core\DependencyInjection;


/**
 * Recursive dependency exception is raised if a container managed object includes itself
 *
 *
 * Class RecursiveDependencyException
 *
 */
class RecursiveDependencyException extends \Exception {

    public function __construct($className) {
        parent::__construct("You have injected an instance of $className into a sub dependency of this itself.");
    }


}
