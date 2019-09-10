<?php
declare(strict_types=1);

/**
 * PHPTAL templating engine
 *
 * Originally developed by Laurent Bedubourg and Kornel Lesiński
 *
 * @category HTML
 * @package  PHPTAL
 * @author   Laurent Bedubourg <lbedubourg@motion-twin.com>
 * @author   Kornel Lesiński <kornel@aardvarkmedia.co.uk>
 * @author   See contributors list @ github
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link     http://phptal.org/
 * @link     https://github.com/SC-Networks/PHPTAL
 */

namespace PhpTal\Dom;

use PhpTal\Php\CodeWriterInterface;
use PhpTal\PHPTAL;

/**
 * Document doctype representation.
 *
 * @internal
 */
final class DocumentType extends Node
{
    /**
     * use CodeWriter to compile this element to PHP code
     *
     * @param CodeWriterInterface $codewriter
     */
    public function generateCode(CodeWriterInterface $codewriter): void
    {
        if ($codewriter->getOutputMode() === PHPTAL::HTML5) {
            $codewriter->setDocType('<!DOCTYPE html>');
        } else {
            $codewriter->setDocType($this->getValueEscaped());
        }
        $codewriter->doDoctype();
    }
}
