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

namespace PhpTal\Php;


use PhpTal\Dom\Element;
use PhpTal\Exception\ConfigurationException;
use PhpTal\Exception\PhpTalException;

/**
 * Helps generate php representation of a template.
 */
interface CodeWriterInterface
{
    /**
     * @return string
     */
    public function createTempVariable(): string;

    /**
     * @param string $var
     *
     * @return void
     * @throws PhpTalException
     */
    public function recycleTempVariable($var): void;

    /**
     * @return string
     */
    public function getCacheFilesBaseName(): string;

    /**
     * @return string
     */
    public function getResult(): string;

    /**
     * set full '<!DOCTYPE...>' string to output later
     *
     * @param string $dt
     *
     * @return void
     */
    public function setDocType(string $dt): void;

    /**
     * set full '<?xml ?>' string to output later
     *
     * @param string $dt
     *
     * @return void
     */
    public function setXmlDeclaration(string $dt): void;

    /**
     * functions later generated and checked for existence will have this prefix added
     * (poor man's namespace)
     *
     * @param string $prefix
     *
     * @return void
     */
    public function setFunctionPrefix(string $prefix): void;

    /**
     * @return string
     */
    public function getFunctionPrefix(): string;

    /**
     * @param string $mode
     *
     * @return string
     * @see \PhpTal\Php\State::setTalesMode()
     *
     */
    public function setTalesMode(string $mode): string;

    /**
     * @param string $src
     *
     * @return array
     */
    public function splitExpression(string $src): array;

    /**
     * @param string $src
     *
     * @return string|array
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     * @throws \PhpTal\Exception\PhpNotAllowedException
     */
    public function evaluateExpression(string $src);

    /**
     * @return void
     */
    public function indent(): void;

    /**
     * @return void
     */
    public function unindent(): void;

    /**
     * @return void
     */
    public function flush(): void;

    /**
     * @param bool $bool
     *
     * @return void
     */
    public function noThrow(bool $bool): void;

    /**
     * @return void
     */
    public function flushCode(): void;

    /**
     * @return void
     */
    public function flushHtml(): void;

    /**
     * Generate code for setting DOCTYPE
     *
     * @param bool $called_from_macro for error checking: unbuffered output doesn't support that
     */
    public function doDoctype(bool $called_from_macro = null): void;

    /**
     * Generate XML declaration
     *
     * @param bool $called_from_macro for error checking: unbuffered output doesn't support that
     */
    public function doXmlDeclaration(bool $called_from_macro = null): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function functionExists(string $name): bool;

    /**
     * @param string $functionName
     * @param Element $treeGen
     *
     * @return void
     * @throws PhpTalException
     * @throws \PhpTal\Exception\TemplateException
     */
    public function doTemplateFile(string $functionName, Element $treeGen): void;

    /**
     * @param string $name
     * @param string $params
     *
     * @return void
     */
    public function doFunction(string $name, string $params): void;

    /**
     * @param string $comment
     *
     * @return void
     */
    public function doComment(string $comment): void;

    /**
     * @return void
     */
    public function doInitTranslator(): void;

    /**
     * @return string
     * @throws ConfigurationException
     */
    public function getTranslatorReference(): string;

    /**
     * @param string $code
     *
     * @return void
     */
    public function doEval(string $code): void;

    /**
     * @param string $out
     * @param string $source
     *
     * @return void
     */
    public function doForeach(string $out, string $source): void;

    /**
     * @param string $expects
     *
     * @return void
     * @throws PhpTalException
     */
    public function doEnd(string $expects = null): void;

    /**
     * @return void
     */
    public function doTry(): void;

    /**
     * @param string $varname
     * @param string $code
     *
     * @return void
     */
    public function doSetVar(string $varname, string $code): void;

    /**
     * @param string $catch
     *
     * @return void
     * @throws PhpTalException
     */
    public function doCatch(string $catch): void;

    /**
     * @param string $condition
     *
     * @return void
     */
    public function doIf(string $condition): void;

    /**
     * @param string $condition
     *
     * @return void
     * @throws PhpTalException
     */
    public function doElseIf(string $condition): void;

    /**
     * @return void
     * @throws PhpTalException
     */
    public function doElse(): void;

    /**
     * @param string $code
     *
     * @return void
     */
    public function doEcho(string $code): void;

    /**
     * @param string $code
     *
     * @return void
     */
    public function doEchoRaw(string $code): void;

    /**
     * @param string $html
     *
     * @return string
     */
    public function interpolateHTML(string $html): string;

    /**
     * @param string $str
     *
     * @return string
     */
    public function interpolateCDATA(string $str): string;

    /**
     * @param string $html
     *
     * @return void
     */
    public function pushHTML(string $html): void;

    /**
     * @param string $codeLine
     *
     * @return void
     */
    public function pushCode(string $codeLine): void;

    /**
     * php string with escaped text
     *
     * @param string $string
     *
     * @return string
     */
    public function str(string $string): string;

    /**
     * @param string $code
     *
     * @return string
     */
    public function escapeCode(string $code): string;

    /**
     * @param string $code
     *
     * @return string
     */
    public function stringifyCode(string $code): string;

    /**
     * @return string
     */
    public function getEncoding(): string;

    /**
     * @param string $src
     *
     * @return string
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     */
    public function interpolateTalesVarsInString(string $src): string;

    /**
     * @param bool $bool
     *
     * @return bool
     */
    public function setDebug(bool $bool): bool;

    /**
     * @return bool
     */
    public function isDebugOn(): bool;

    /**
     * @return int
     */
    public function getOutputMode(): int;

    /**
     * @param string $value
     *
     * @return string
     */
    public function quoteAttributeValue(string $value): string;

    /**
     * @return void
     */
    public function pushContext(): void;

    /**
     * @return void
     */
    public function popContext(): void;
}
