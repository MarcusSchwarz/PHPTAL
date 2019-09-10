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

use PhpTal\Dom\Element;
use PhpTal\Dom\Node;
use PhpTal\Exception\TemplateException;
use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;
use PhpTal\TalNamespace\Builtin;

/**
 * METAL Specification 1.0
 *
 *      argument ::= expression
 *
 * Example:
 *
 *      <hr />
 *      <p metal:use-macro="here/master_page/macros/copyright">
 *      <hr />
 *
 * PHPTAL: (here not supported)
 *
 *      <?php echo phptal_macro( $tpl, 'master_page.html/macros/copyright'); ? >
 *
 *
 *
 * @internal
 */
final class UseMacro extends Attribute
{
    /**
     * @var array
     */
    public static $ALLOWED_ATTRIBUTES = [
        'fill-slot' => Builtin::NS_METAL,
        'define-macro' => Builtin::NS_METAL,
        'define' => Builtin::NS_TAL,
    ];

    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws TemplateException
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\PhpTalException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        $this->pushSlots($codewriter);

        foreach ($this->phpelement->childNodes as $child) {
            $this->generateFillSlots($codewriter, $child);
        }

        $macroname = str_replace('-', '_', $this->expression);

        // throw error if attempting to define and use macro at same time
        // [should perhaps be a TemplateException? but I don't know how to set that up...]
        $defineAttr = $this->phpelement->getAttributeNodeNS(Builtin::NS_METAL, 'define-macro');
        if ($defineAttr && $defineAttr->getValue() === $macroname) {
            throw new TemplateException(
                "Cannot simultaneously define and use macro '$macroname'",
                $this->phpelement->getSourceFile(),
                $this->phpelement->getSourceLine()
            );
        }

        // local macro (no filename specified) and non dynamic macro name
        // can be called directly if it's a known function (just generated or seen in previous compilation)
        if (preg_match('/^[a-z0-9_]+$/i', $macroname) && $codewriter->functionExists($macroname)) {
            $code = $codewriter->getFunctionPrefix() . $macroname . '($_thistpl, $tpl)';
            $codewriter->pushCode($code);
        } // external macro or ${macroname}, use PHPTAL at runtime to resolve it
        else {
            $code = $codewriter->interpolateTalesVarsInString($this->expression);
            $codewriter->pushCode('$tpl->executeMacroOfTemplate(' . $code . ', $_thistpl)');
        }

        $this->popSlots($codewriter);
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

    /**
     * reset template slots on each macro call ?
     *
     * NOTE: defining a macro and using another macro on the same tag
     * means inheriting from the used macro, thus slots are shared, it
     * is a little tricky to understand but very natural to use.
     *
     * For example, we may have a main design.html containing our main
     * website presentation with some slots (menu, content, etc...) then
     * we may define a member.html macro which use the design.html macro
     * for the general layout, fill the menu slot and let caller templates
     * fill the parent content slot without interfering.
     *
     * @param CodeWriterInterface $codewriter
     */
    private function pushSlots(CodeWriterInterface $codewriter): void
    {
        if (!$this->phpelement->hasAttributeNS(Builtin::NS_METAL, 'define-macro')) {
            $codewriter->pushCode('$ctx->pushSlots()');
        }
    }

    /**
     * generate code that pops macro slots
     * (restore slots if not inherited macro)
     *
     * @param CodeWriterInterface $codewriter
     */
    private function popSlots(CodeWriterInterface $codewriter): void
    {
        if (!$this->phpelement->hasAttributeNS(Builtin::NS_METAL, 'define-macro')) {
            $codewriter->pushCode('$ctx->popSlots()');
        }
    }

    /**
     * recursively generates code for slots
     *
     * @param CodeWriterInterface $codewriter
     * @param Node|Element $phpelement
     *
     * @throws \PhpTal\Exception\PhpTalException
     * @throws TemplateException
     */
    private function generateFillSlots(CodeWriterInterface $codewriter, Node $phpelement): void
    {
        if ($phpelement instanceof Element === false) {
            return;
        }

        // if the tag contains one of the allowed attribute, we generate it
        foreach (self::$ALLOWED_ATTRIBUTES as $qname => $uri) {
            if ($phpelement->hasAttributeNS($uri, $qname)) {
                $phpelement->generateCode($codewriter);
                return;
            }
        }

        foreach ($phpelement->childNodes as $child) {
            $this->generateFillSlots($codewriter, $child);
        }
    }
}
