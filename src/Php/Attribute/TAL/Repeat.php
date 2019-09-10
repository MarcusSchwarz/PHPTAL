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

/**
 * TAL Specifications 1.4
 *
 *       argument      ::= variable_name expression
 *       variable_name ::= Name
 *
 *  Example:
 *
 *       <p tal:repeat="txt python:'one', 'two', 'three'">
 *          <span tal:replace="txt" />
 *       </p>
 *       <table>
 *         <tr tal:repeat="item here/cart">
 *             <td tal:content="repeat/item/index">1</td>
 *             <td tal:content="item/description">Widget</td>
 *             <td tal:content="item/price">$1.50</td>
 *         </tr>
 *       </table>
 *
 *  The following information is available from an Iterator:
 *
 *     * index - repetition number, starting from zero.
 *     * number - repetition number, starting from one.
 *     * even - true for even-indexed repetitions (0, 2, 4, ...).
 *     * odd - true for odd-indexed repetitions (1, 3, 5, ...).
 *     * start - true for the starting repetition (index 0).
 *     * end - true for the ending, or final, repetition.
 *     * length - length of the sequence, which will be the total number of repetitions.
 *
 *     * letter - count reps with lower-case letters: "a" - "z", "aa" - "az", "ba" - "bz", ..., "za" - "zz", "aaa" - "aaz", and so forth.
 *     * Letter - upper-case version of letter.
 *     * roman - count reps with lower-case roman numerals: "i", "ii", "iii", "iv", "v", "vi" ...
 *     * Roman - upper-case version of roman numerals.
 *     * first - true for the first item in a group - see note below
 *     * lasst - true for the last item in a group - see note below
 *
 *   Note: first and last are intended for use with sorted sequences. They try to
 *   divide the sequence into group of items with the same value. If you provide
 *   a path, then the value obtained by following that path from a sequence item
 *   is used for grouping, otherwise the value of the item is used. You can
 *   provide the path by appending it to the path from the repeat variable,
 *   as in "repeat/item/first/color".
 *
 *  PHPTAL: index, number, even, etc... will be stored in the
 *  $ctx->repeat->'item'  object.  Thus $ctx->repeat->item->odd
 *
 *
 *
 *
 * @internal
 */
final class Repeat extends Attribute
{
    /**
     * @var string
     */
    private $var;

    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\PhpNotAllowedException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        $this->var = $codewriter->createTempVariable();

        // alias to repeats handler to avoid calling extra getters on each variable access
        $codewriter->doSetVar($this->var, '$ctx->repeat');

        [$varName, $expression] = $this->parseSetExpression($this->expression);
        $code = $codewriter->evaluateExpression($expression ?? '');

        // instantiate controller using expression
        $codewriter->doSetVar($this->var.'->'.$varName, 'new \PhpTal\RepeatController('.$code.')'."\n");

        $codewriter->pushContext();

        // Lets loop the iterator with a foreach construct
        $codewriter->doForeach('$ctx->'.$varName, $this->var.'->'.$varName);
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
        $codewriter->doEnd('foreach');
        $codewriter->popContext();

        $codewriter->recycleTempVariable($this->var);
    }
}
