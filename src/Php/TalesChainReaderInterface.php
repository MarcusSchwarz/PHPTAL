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

interface TalesChainReaderInterface
{
    /**
     * @param TalesChainExecutorInterface $executor
     *
     * @return void
     */
    public function talesChainNothingKeyword(TalesChainExecutorInterface $executor): void;

    /**
     * @param TalesChainExecutorInterface $executor
     *
     * @return void
     */
    public function talesChainDefaultKeyword(TalesChainExecutorInterface $executor): void;

    /**
     * @param TalesChainExecutorInterface $executor
     * @param string $expression
     * @param bool $islast
     *
     * @return void
     */
    public function talesChainPart(TalesChainExecutorInterface $executor, string $expression, bool $islast): void;
}
