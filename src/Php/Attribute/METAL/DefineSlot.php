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

use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;

/**
 * METAL Specification 1.0
 *
 *      argument ::= Name
 *
 * Example:
 *
 *      <table metal:define-macro="sidebar">
 *        <tr><th>Links</th></tr>
 *        <tr><td metal:define-slot="links">
 *          <a href="/">A Link</a>
 *        </td></tr>
 *      </table>
 *
 * PHPTAL: (access to slots may be renamed)
 *
 *  <?php function XXXX_macro_sidebar($tpl) { ? >
 *      <table>
 *        <tr><th>Links</th></tr>
 *        <tr>
 *        <?php if (isset($tpl->slots->links)): ? >
 *          <?php echo $tpl->slots->links ? >
 *        <?php else: ? >
 *        <td>
 *          <a href="/">A Link</a>
 *        </td></tr>
 *      </table>
 *  <?php } ? >
 *
 * @internal
 */
final class DefineSlot extends Attribute
{
    /**
     * @var string
     */
    private $tmp_var;

    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\PhpTalException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        $this->tmp_var = $codewriter->createTempVariable();

        $codewriter->doSetVar($this->tmp_var, $codewriter->interpolateTalesVarsInString($this->expression));
        $codewriter->doIf('$ctx->hasSlot('.$this->tmp_var.')');
        $codewriter->pushCode('$ctx->echoSlot('.$this->tmp_var.')');
        $codewriter->doElse();
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
        $codewriter->doEnd('if');

        $codewriter->recycleTempVariable($this->tmp_var);
    }
}
