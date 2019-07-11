<?php

namespace Kinikit\Core;

use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\DependencyInjection\Container;


class InitTest extends \PHPUnit\Framework\TestCase {

    public function testInitSetsTimeZoneAccordingToConfigParamOrDefaultToLondon() {

        new Init();

        // Check that timezone is now correctly set.
        $this->assertEquals("Europe/London", date_default_timezone_get());


        Configuration::instance()->addParameter("default.timezone", "Europe/Paris");

        new Init();

        // Check that timezone is now correctly set.
        $this->assertEquals("Europe/Paris", date_default_timezone_get());

    }


    public function testIfBootstrapClassExistsInApplicationNamespaceItIsCalledWithPreAndPostFunctions() {

        // Set up the pre-conditions to test that the pre and post were called.
        date_default_timezone_set("Europe/London");
        Configuration::instance()->addParameter("default.timezone", "Europe/Paris");

        new Init();

        // Should be the single container managed version.
        $bootstrap = Container::instance()->get(Bootstrap::class);

        // Confirm that pre and post were called in the correct order.
        $this->assertEquals("Europe/London", $bootstrap->timezoneBefore);
        $this->assertEquals("Europe/Paris", $bootstrap->timezoneAfter);


    }


}
