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

use PhpTal\Dom\Defs;
use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;
use PhpTal\Php\TalesChainExecutor;
use PhpTal\Php\TalesChainExecutorInterface;
use PhpTal\Php\TalesChainReaderInterface;
use PhpTal\Php\TalesInternal;
use PhpTal\PHPTAL;
use PhpTal\TalNamespace\Builtin;

/**
 * TAL Specifications 1.4
 *
 *       argument             ::= attribute_statement [';' attribute_statement]*
 *       attribute_statement  ::= attribute_name expression
 *       attribute_name       ::= [namespace ':'] Name
 *       namespace            ::= Name
 *
 * examples:
 *
 *      <a href="/sample/link.html"
 *         tal:attributes="href here/sub/absolute_url">
 *      <textarea rows="80" cols="20"
 *         tal:attributes="rows request/rows;cols request/cols">
 *
 * IN PHPTAL: attributes will not work on structured replace.
 *
 * @interal
 */
final class Attributes extends Attribute implements TalesChainReaderInterface
{
    /**
     * before creates several variables that need to be freed in after
     *
     * @var array
     */
    private $vars_to_recycle = [];

    /**
     * value for default keyword
     *
     * @var string
     */
    private $default_escaped;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $attkey;

    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     * @throws \ReflectionException
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        // split attributes using ; delimiter
        foreach ($codewriter->splitExpression($this->expression) as $exp) {
            [$qname, $expression] = $this->parseSetExpression($exp);
            if ($expression) {
                $this->prepareAttribute($codewriter, $qname, $expression);
            }
        }
    }

    /**
     * @param CodeWriterInterface $codewriter
     * @param string $qname
     * @param string $expression
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     * @throws \ReflectionException
     */
    private function prepareAttribute(CodeWriterInterface $codewriter, string $qname, string $expression): void
    {
        $tales_code = $this->extractEchoType($expression);
        $code = $codewriter->evaluateExpression($tales_code);

        // XHTML boolean attribute does not appear when empty or false
        if (Defs::getInstance()->isBooleanAttribute($qname)) {
            // I don't want to mix code for boolean with chained executor
            // so compile it again to simple expression
            if (is_array($code)) {
                $code = TalesInternal::compileToPHPExpression($tales_code);
            }
            $this->prepareBooleanAttribute($codewriter, $qname, $code);
            return;
        }

        // if $code is an array then the attribute value is decided by a
        // tales chained expression
        if (is_array($code)) {
            $this->prepareChainedAttribute($codewriter, $qname, $code);
            return;
        }

        // i18n needs to read replaced value of the attribute, which is not possible
        // if attribute is completely replaced with conditional code
        if ($this->phpelement->hasAttributeNS(Builtin::NS_I18N, 'attributes')) {
            $this->prepareAttributeUnconditional($codewriter, $qname, $code);
        } else {
            $this->prepareAttributeConditional($codewriter, $qname, $code);
        }
    }

    /**
     * attribute will be output regardless of its evaluated value. NULL behaves just like "".
     *
     * @param CodeWriterInterface $codewriter
     * @param string $qname
     * @param string $code
     */
    private function prepareAttributeUnconditional(CodeWriterInterface $codewriter, string $qname, string $code): void
    {
        // regular attribute which value is the evaluation of $code
        $attkey = $this->getVarName($codewriter);
        if ($this->echoType === Attribute::ECHO_STRUCTURE) {
            $value = $codewriter->stringifyCode($code);
        } else {
            $value = $codewriter->escapeCode($code);
        }
        $codewriter->doSetVar($attkey, $value);
        $this->phpelement->getOrCreateAttributeNode($qname)->overwriteValueWithVariable($attkey);
    }

    /**
     * If evaluated value of attribute is NULL, it will not be output at all.
     *
     * @param CodeWriterInterface $codewriter
     * @param string $qname
     * @param string $code
     *
     * @throws \PhpTal\Exception\PhpTalException
     */
    private function prepareAttributeConditional(CodeWriterInterface $codewriter, string $qname, string $code): void
    {
        // regular attribute which value is the evaluation of $code
        $attkey = $this->getVarName($codewriter);

        $codewriter->doIf("null !== ($attkey = ($code))");

        if ($this->echoType !== Attribute::ECHO_STRUCTURE) {
            $codewriter->doSetVar($attkey, $codewriter->str(" $qname=\"") . '.' .
                $codewriter->escapeCode($attkey) . ".'\"'");
        } else {
            $codewriter->doSetVar($attkey, $codewriter->str(" $qname=\"") . '.' .
                $codewriter->stringifyCode($attkey) . ".'\"'");
        }

        $codewriter->doElse();
        $codewriter->doSetVar($attkey, "''");
        $codewriter->doEnd('if');

        $this->phpelement->getOrCreateAttributeNode($qname)->overwriteFullWithVariable($attkey);
    }

    /**
     * @param CodeWriterInterface $codewriter
     * @param string $qname
     * @param array $chain
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    private function prepareChainedAttribute(CodeWriterInterface $codewriter, string $qname, array $chain): void
    {
        $this->default_escaped = false;
        $this->attribute = $qname;
        $default_attr = $this->phpelement->getAttributeNode($qname);
        if ($default_attr) {
            $this->default_escaped = $default_attr->getValueEscaped();
        }
        $this->attkey = $this->getVarName($codewriter);
        // todo this does magic in the constructor :/
        new TalesChainExecutor($codewriter, $chain, $this);
        $this->phpelement->getOrCreateAttributeNode($qname)->overwriteFullWithVariable($this->attkey);
    }

    /**
     * @param CodeWriterInterface $codewriter
     * @param string $qname
     * @param string $code
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    private function prepareBooleanAttribute(CodeWriterInterface $codewriter, string $qname, string $code): void
    {
        $attkey = $this->getVarName($codewriter);

        if ($codewriter->getOutputMode() === PHPTAL::HTML5) {
            $value = "' $qname'";
        } else {
            $value = "' $qname=\"$qname\"'";
        }
        $codewriter->doIf($code);
        $codewriter->doSetVar($attkey, $value);
        $codewriter->doElse();
        $codewriter->doSetVar($attkey, '\'\'');
        $codewriter->doEnd('if');
        $this->phpelement->getOrCreateAttributeNode($qname)->overwriteFullWithVariable($attkey);
    }

    /**
     * @param CodeWriterInterface $codewriter
     *
     * @return string
     */
    private function getVarName(CodeWriterInterface $codewriter): string
    {
        $var = $codewriter->createTempVariable();
        $this->vars_to_recycle[] = $var;
        return $var;
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
        foreach ($this->vars_to_recycle as $var) {
            $codewriter->recycleTempVariable($var);
        }
    }

    /**
     * @param TalesChainExecutorInterface $executor
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    public function talesChainNothingKeyword(TalesChainExecutorInterface $executor): void
    {
        $codewriter = $executor->getCodeWriter();
        $executor->doElse();
        $codewriter->doSetVar(
            $this->attkey,
            "''"
        );
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
        $codewriter = $executor->getCodeWriter();
        $executor->doElse();
        $attr_str = ($this->default_escaped !== false)
            ? ' ' . $this->attribute . '=' . $codewriter->quoteAttributeValue($this->default_escaped)  // default value
            : '';                                 // do not print attribute
        $codewriter->doSetVar($this->attkey, $codewriter->str($attr_str));
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
        $codewriter = $executor->getCodeWriter();

        if (!$islast) {
            $condition = "!\PhpTal\Helper::phptal_isempty($this->attkey = ($expression))";
        } else {
            $condition = "null !== ($this->attkey = ($expression))";
        }
        $executor->doIf($condition);

        if ($this->echoType === Attribute::ECHO_STRUCTURE) {
            $value = $codewriter->stringifyCode($this->attkey);
        } else {
            $value = $codewriter->escapeCode($this->attkey);
        }

        $codewriter->doSetVar($this->attkey, $codewriter->str(" {$this->attribute}=\"") . ".$value.'\"'");
    }
}
