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

/** TAL Specifications 1.4
 *
 *     argument ::= (['text'] | 'structure') expression
 *
 * Example:
 *
 *     <p tal:content="user/name">Fred Farkas</p>
 *
 *
 *
 *
 * @internal
 */
    final class Content extends Attribute implements TalesChainReaderInterface
{
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
        $expression = $this->extractEchoType($this->expression);

        $code = $codewriter->evaluateExpression($expression);

        if (is_array($code)) {
            $this->generateChainedContent($codewriter, $code);
            return;
        }

        if ($code === TalesInternal::NOTHING_KEYWORD) {
            return;
        }

        if ($code === TalesInternal::DEFAULT_KEYWORD) {
            $this->generateDefault($codewriter);
            return;
        }

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
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     */
    private function generateDefault(CodeWriterInterface $codewriter): void
    {
        $this->phpelement->generateContent($codewriter, true);
    }

    /**
     * @param CodeWriterInterface $codewriter
     * @param array $code
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    protected function generateChainedContent(CodeWriterInterface $codewriter, array $code): void
    {
        // todo yep, indeed, this thing executes logic, a lot of it, in the constructor
        new TalesChainExecutor($codewriter, $code, $this);
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
     * @param TalesChainExecutorInterface $executor
     *
     * @return void
     */
    public function talesChainNothingKeyword(TalesChainExecutorInterface $executor): void
    {
        $executor->breakChain();
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
}
