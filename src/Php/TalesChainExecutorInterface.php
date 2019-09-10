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

interface TalesChainExecutorInterface
{
    /**
     * @return CodeWriterInterface
     */
    public function getCodeWriter(): CodeWriterInterface;

    /**
     * @param string $condition
     *
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    public function doIf(string $condition): void;

    /**
     * @return void
     * @throws \PhpTal\Exception\PhpTalException
     */
    public function doElse(): void;

    /**
     * @return void
     */
    public function breakChain(): void;

    /**
     * @return void
     */
    public function continueChain(): void;
}
