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
 * i18n:target
 *
 * The i18n:target attribute specifies the language of the translation we
 * want to get. If the value is "default", the language negotiation services
 * will be used to choose the destination language. If the value is
 * "nothing", no translation will be performed; this can be used to suppress
 * translation within a larger translated unit. Any other value must be a
 * language code.
 *
 * The attribute value is a TALES expression; the result of evaluating the
 * expression is the language code or one of the reserved values.
 *
 * Note that i18n:target is primarily used for hints to text extraction
 * tools and translation teams. If you had some text that should only be
 * translated to e.g. German, then it probably shouldn't be wrapped in an
 * i18n:translate span.
 *
 *
 * @internal
 */
final class Target extends Attribute
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
     * Called after element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     */
    public function after(CodeWriterInterface $codewriter): void
    {
    }
}
