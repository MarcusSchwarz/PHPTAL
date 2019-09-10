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

namespace PhpTal\Php\Attribute\PHPTAL;

use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;

/**
 * @internal
 */
final class Id extends Attribute
{
    /**
     * @var
     */
    private $var;

    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        // retrieve trigger
        $this->var = $codewriter->createTempVariable();

        $codewriter->doSetVar(
            $this->var,
            '$tpl->getTrigger('.$codewriter->str($this->expression).')'
        );

        // if trigger found and trigger tells to proceed, we execute
        // the node content
        $codewriter->doIf($this->var.' &&
            '.$this->var.'->start('.$codewriter->str($this->expression).', $tpl) === \PhpTal\TriggerInterface::PROCEED');
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
        // end of if PROCEED
        $codewriter->doEnd('if');

        // if trigger found, notify the end of the node
        $codewriter->doIf($this->var);
        $codewriter->pushCode(
            $this->var.'->end('.$codewriter->str($this->expression).', $tpl)'
        );
        $codewriter->doEnd('if');
        $codewriter->recycleTempVariable($this->var);
    }
}
