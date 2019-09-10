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

/**
 * XML declaration node.
 *
 * @internal
 */
final class XmlDeclaration extends Node
{
    /**
     * use CodeWriter to compile this element to PHP code
     *
     * @param CodeWriterInterface $codewriter
     */
    public function generateCode(CodeWriterInterface $codewriter): void
    {
        $codewriter->setXmlDeclaration($this->getValueEscaped());
        $codewriter->doXmlDeclaration();
    }
}
