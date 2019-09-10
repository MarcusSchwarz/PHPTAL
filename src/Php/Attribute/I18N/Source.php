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
 * i18n:source
 *
 *  The i18n:source attribute specifies the language of the text to be
 *  translated. The default is "nothing", which means we don't provide
 *  this information to the translation services.
 *
 *
 * @internal
 */
final class Source extends Attribute
{
    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws \PhpTal\Exception\ConfigurationException
     * @throws \PhpTal\Exception\PhpTalException
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        // ensure that a sources stack exists or create it
        $codewriter->doIf('!isset($_i18n_sources)');
        $codewriter->pushCode('$_i18n_sources = array()');
        $codewriter->doEnd();

        // push current source and use new one
        $codewriter->pushCode(
            '$_i18n_sources[] = ' . $codewriter->getTranslatorReference() . '->setSource(' . $codewriter->str($this->expression) . ')'
        );
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
        // restore source
        $code = $codewriter->getTranslatorReference() . '->setSource(array_pop($_i18n_sources))';
        $codewriter->pushCode($code);
    }
}
