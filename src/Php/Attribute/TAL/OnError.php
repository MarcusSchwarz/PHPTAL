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

namespace PhpTal\Php\Attribute\TAL;

use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;
use PhpTal\Php\TalesInternal;

/**
 * TAL Specifications 1.4
 *
 *      argument ::= (['text'] | 'structure') expression
 *
 * Example:
 *
 *      <p tal:on-error="string: Error! This paragraph is buggy!">
 *      My name is <span tal:replace="here/SlimShady" />.<br />
 *      (My login name is
 *      <b tal:on-error="string: Username is not defined!"
 *         tal:content="user">Unknown</b>)
 *      </p>
 *
 * @internal
 */
final class OnError extends Attribute
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
        $codewriter->doTry();
        $codewriter->pushCode('ob_start()');
    }

    /**
     * Called after element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\PhpNotAllowedException
     * @throws \PhpTal\Exception\PhpTalException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     */
    public function after(CodeWriterInterface $codewriter): void
    {
        $var = $codewriter->createTempVariable();

        $codewriter->pushCode('ob_end_flush()');
        $codewriter->doCatch('Exception '.$var);
        $codewriter->pushCode('$tpl->addError('.$var.')');
        $codewriter->pushCode('ob_end_clean()');

        $expression = $this->extractEchoType($this->expression);

        $code = $codewriter->evaluateExpression($expression);
        switch ($code) {
            case TalesInternal::NOTHING_KEYWORD:
                break;

            case TalesInternal::DEFAULT_KEYWORD:
                $codewriter->pushHTML('<pre class="phptalError">');
                $codewriter->doEcho($var);
                $codewriter->pushHTML('</pre>');
                break;

            default:
                $this->doEchoAttribute($codewriter, $code);
                break;
        }
        $codewriter->doEnd('catch');

        $codewriter->recycleTempVariable($var);
    }
}
