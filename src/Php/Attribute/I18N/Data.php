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

namespace PhpTal\Php\Attribute\I18N;

use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;

/**
 * i18n:data
 *
 * Since TAL always returns strings, we need a way in ZPT to translate
 * objects, the most obvious case being DateTime objects. The data attribute
 * will allow us to specify such an object, and i18n:translate will provide
 * us with a legal format string for that object. If data is used,
 * i18n:translate must be used to give an explicit message ID, rather than
 * relying on a message ID computed from the content.
 *
 *
 *
 * @internal
 */
final class Data extends Attribute
{
    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     */
    public function before(CodeWriterInterface $codewriter): void
    {
    }

    /**
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     */
    public function after(CodeWriterInterface $codewriter): void
    {
    }
}
