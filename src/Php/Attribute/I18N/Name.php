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

/** i18n:name
 *
 * Name the content of the current element for use in interpolation within
 * translated content. This allows a replaceable component in content to be
 * re-ordered by translation. For example:
 *
 * <span i18n:translate=''>
 *   <span tal:replace='here/name' i18n:name='name' /> was born in
 *   <span tal:replace='here/country_of_birth' i18n:name='country' />.
 * </span>
 *
 * would cause this text to be passed to the translation service:
 *
 *     "${name} was born in ${country}."
 *
 *
 * @internal
 */
final class Name extends Attribute
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
        $codewriter->pushCode('ob_start()');
    }

    /**
     * Called after element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws \PhpTal\Exception\ConfigurationException
     */
    public function after(CodeWriterInterface $codewriter): void
    {
        $codewriter->pushCode(
            $codewriter->getTranslatorReference() . '->setVar(' . $codewriter->str($this->expression) . ', ob_get_clean())'
        );
    }
}
