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
use PhpTal\Php\TalesChainExecutor;
use PhpTal\Php\TalesChainExecutorInterface;
use PhpTal\Php\TalesChainReaderInterface;
use PhpTal\Php\TalesInternal;

/**
 * TAL Specifications 1.4
 *
 *      argument ::= (['text'] | 'structure') expression
 *
 *  Default behaviour : text
 *
 *      <span tal:replace="template/title">Title</span>
 *      <span tal:replace="text template/title">Title</span>
 *      <span tal:replace="structure table" />
 *      <span tal:replace="nothing">This element is a comment.</span>
 *
 *
 *
 * @internal
 */
final class Replace extends Attribute implements TalesChainReaderInterface
{
    /**
     * Called before element printing.
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
    public function before(CodeWriterInterface $codewriter): void
    {
        // tal:replace="" => do nothing and ignore node
        if (trim($this->expression) === '') {
            return;
        }

        $expression = $this->extractEchoType($this->expression);
        $code = $codewriter->evaluateExpression($expression);

        // chained expression
        if (is_array($code)) {
            $this->replaceByChainedExpression($codewriter, $code);
            return;
        }

        // nothing do nothing
        if ($code === TalesInternal::NOTHING_KEYWORD) {
            return;
        }

        // default generate default tag content
        if ($code === TalesInternal::DEFAULT_KEYWORD) {
            $this->generateDefault($codewriter);
            return;
        }

        // replace tag with result of expression
        $this->doEchoAttribute($codewriter, $code);
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
     * support expressions like "foo | bar"
     *
     * @param CodeWriterInterface $codewriter
     * @param array $expArray
     *
     * @throws \PhpTal\Exception\PhpTalException
     */
    private function replaceByChainedExpression(CodeWriterInterface $codewriter, $expArray)
    {
        new TalesChainExecutor($codewriter, $expArray, $this);
    }

    /**
     * @param TalesChainExecutorInterface $executor
     *
     * @return void
     */
    public function talesChainNothingKeyword(TalesChainExecutorInterface $executor): void
    {
        $executor->continueChain();
    }

    /**
     * @param TalesChainExecutorInterface $executor
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    public function talesChainDefaultKeyword(TalesChainExecutorInterface $executor): void
    {
        $executor->doElse();
        $this->generateDefault($executor->getCodeWriter());
        $executor->breakChain();
    }

    /**
     * @param TalesChainExecutorInterface $executor
     * @param string $expression
     * @param bool $islast
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    public function talesChainPart(TalesChainExecutorInterface $executor, string $expression, bool $islast): void
    {
        if (!$islast) {
            $var = $executor->getCodeWriter()->createTempVariable();
            $executor->doIf('!\PhpTal\Helper::phptal_isempty('.$var.' = '.$expression.')');
            $this->doEchoAttribute($executor->getCodeWriter(), $var);
            $executor->getCodeWriter()->recycleTempVariable($var);
        } else {
            $executor->doElse();
            $this->doEchoAttribute($executor->getCodeWriter(), $expression);
        }
    }

    /**
     * don't replace - re-generate default content
     *
     * @param CodeWriterInterface $codewriter
     *
     * @throws \PhpTal\Exception\PhpTalException
     */
    private function generateDefault(CodeWriterInterface $codewriter): void
    {
        $this->phpelement->generateSurroundHead($codewriter);
        $this->phpelement->generateHead($codewriter);
        $this->phpelement->generateContent($codewriter);
        $this->phpelement->generateFoot($codewriter);
        $this->phpelement->generateSurroundFoot($codewriter);
    }
}
