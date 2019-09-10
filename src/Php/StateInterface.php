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

interface StateInterface
{
    /**
     * used by codewriter to get information for phptal:cache
     */
    public function getCacheFilesBaseName(): string;

    /**
     * true if PHPTAL has translator set
     */
    public function isTranslationOn(): bool;

    /**
     * controlled by phptal:debug
     *
     * @param bool $bool
     *
     * @return bool
     */
    public function setDebug(bool $bool): bool;

    /**
     * if true, add additional diagnostic information to generated code
     *
     * @return bool
     */
    public function isDebugOn(): bool;

    /**
     * Sets new and returns old TALES mode.
     * Valid modes are 'tales' and 'php'
     *
     * @param string $mode
     *
     * @return string
     */
    public function setTalesMode(string $mode): string;

    /**
     * @return string
     */
    public function getTalesMode(): string;

    /**
     * encoding used for both template input and output
     *
     * @return string
     */
    public function getEncoding(): string;

    /**
     * Syntax rules to follow in generated code
     *
     * @return int one of \PhpTal\PHPTAL::XHTML, \PhpTal\PHPTAL::XML, \PhpTal\PHPTAL::HTML5
     */
    public function getOutputMode(): int;

    /**
     * compile TALES expression according to current talesMode
     *
     * @param string $expression
     *
     * @return string|array string with PHP code or array with expressions for TalesChainExecutor
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     * @throws \PhpTal\Exception\PhpNotAllowedException
     */
    public function evaluateExpression(?string $expression);

    /**
     * returns PHP code that generates given string, including dynamic replacements
     *
     * It's almost unused.
     *
     * @param string $string
     *
     * @return string
     * @throws \PhpTal\Exception\ParserException
     * @throws \PhpTal\Exception\UnknownModifierException
     * @throws \ReflectionException
     */
    public function interpolateTalesVarsInString(string $string): string;

    /**
     * replaces ${} in string, expecting HTML-encoded input and HTML-escapes output
     *
     * @param string $src
     *
     * @return string
     */
    public function interpolateTalesVarsInHTML(string $src): string;

    /**
     * replaces ${} in string, expecting CDATA (basically unescaped) input,
     * generates output protected against breaking out of CDATA in XML/HTML
     * (depending on current output mode).
     *
     * @param string $src
     *
     * @return string
     */
    public function interpolateTalesVarsInCDATA(string $src): string;

    /**
     * expects PHP code and returns PHP code that will generate escaped string
     * Optimizes case when PHP string is given.
     *
     * @param string $php
     *
     * @return string php code
     */
    public function htmlchars(string $php): string;

    /**
     * allow proper printing of any object
     * (without escaping - for use with structure keyword)
     *
     * @param string $php
     *
     * @return string php code
     */
    public function stringify(string $php): string;
}
