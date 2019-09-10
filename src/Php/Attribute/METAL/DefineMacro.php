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

namespace PhpTal\Php\Attribute\METAL;

use PhpTal\Exception\ParserException;
use PhpTal\Exception\TemplateException;
use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;

/**
 * METAL Specification 1.0
 *
 *      argument ::= Name
 *
 * Example:
 *
 *      <p metal:define-macro="copyright">
 *      Copyright 2001, <em>Foobar</em> Inc.
 *      </p>
 *
 * PHPTAL:
 *
 *      <?php function XXX_macro_copyright($tpl) { ? >
 *        <p>
 *        Copyright 2001, <em>Foobar</em> Inc.
 *        </p>
 *      <?php } ? >
 *
 * @internal
 */
final class DefineMacro extends Attribute
{
    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws ParserException
     * @throws TemplateException
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        $macroname = str_replace('-', '_', trim($this->expression));
        if (!preg_match('/^[a-z0-9_]+$/i', $macroname)) {
            throw new ParserException(
                'Bad macro name "'.$macroname.'"',
                $this->phpelement->getSourceFile(),
                $this->phpelement->getSourceLine()
            );
        }

        if ($codewriter->functionExists($macroname)) {
            throw new TemplateException(
                "Macro $macroname is defined twice",
                $this->phpelement->getSourceFile(),
                $this->phpelement->getSourceLine()
            );
        }

        $codewriter->doFunction($macroname, '\PhpTal\PHPTAL $_thistpl, \PhpTal\PHPTAL $tpl');
        $codewriter->doSetVar('$tpl', 'clone $tpl');
        $codewriter->doSetVar('$ctx', '$tpl->getContext()');
        $codewriter->doInitTranslator();
        $codewriter->doXmlDeclaration(true);
        $codewriter->doDoctype(true);
    }

    /**
     * Called after element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    public function after(CodeWriterInterface $codewriter): void
    {
        $codewriter->doEnd('function');
    }
}
