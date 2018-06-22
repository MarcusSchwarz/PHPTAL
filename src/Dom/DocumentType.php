<?php
declare(strict_types=1);

/**
 * PHPTAL templating engine
 *
 * PHP Version 5
 *
 * @category HTML
 * @package  PHPTAL
 * @author   Laurent Bedubourg <lbedubourg@motion-twin.com>
 * @author   Kornel Lesiński <kornel@aardvarkmedia.co.uk>
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link     http://phptal.org/
 */

namespace PhpTal\Dom;

use PhpTal\Php\CodeWriter;
use PhpTal\PHPTAL;

/**
 * Document doctype representation.
 *
 * @package PHPTAL
 */
class DocumentType extends Node
{
    /**
     * use CodeWriter to compile this element to PHP code
     *
     * @param CodeWriter $codewriter
     */
    public function generateCode(CodeWriter $codewriter): void
    {
        if ($codewriter->getOutputMode() === PHPTAL::HTML5) {
            $codewriter->setDocType('<!DOCTYPE html>');
        } else {
            $codewriter->setDocType($this->getValueEscaped());
        }
        $codewriter->doDoctype();
    }
}