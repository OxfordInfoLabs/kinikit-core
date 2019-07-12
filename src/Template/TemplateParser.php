<?php

namespace Kinikit\Core\Template;

use Kinikit\Core\Configuration;
use Kinikit\Core\Template\Parser\MustacheTemplateParser;
use Kinikit\Core\Template\Parser\TwigTemplateParser;


/**
 * Interface for view parsers which may be injected into the Model and View class statically for custom view parsing in applications.
 *
 * @defaultImplementation Kinikit\Core\Template\MustacheTemplateParser
 *
 * @implementationConfigParam template.parser
 * @implementation mustache Kinikit\Core\Template\MustacheTemplateParser
 * @implementation twig Kinikit\Core\Template\TwigTemplateParser
 * @implementation php Kinikit\Core\Template\PHPTemplateParser
 *
 *
 * Interface TemplateParser
 */
interface TemplateParser {

    // Parse the template text and return the modified string.  The model is passed as a reference to allow mutability of the model if required.
    public function parseTemplateText($templateText, &$model);

}
