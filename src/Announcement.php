<?php

namespace Kinikit\Core;

/**
 * Announcement interface, implemented by application announcement classes.
 * The application announcement runs before any other logic for CLI, MVC and WebService requests.
 *
 * @author markrobertshaw
 *
 */
interface Announcement {

    /**
     * Required function to be implemented by any announcement in the system
     */
    public function announce();

}

?>