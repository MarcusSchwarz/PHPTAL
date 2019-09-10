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

use PhpTal\Exception\TemplateException;
use PhpTal\Php\Attribute;
use PhpTal\Php\CodeWriterInterface;

/**
 * @internal
 */
final class Tales extends Attribute
{

    /**
     * @var string
     */
    private $oldMode;

    /**
     * Called before element printing.
     *
     * @param CodeWriterInterface $codewriter
     *
     * @return void
     * @throws TemplateException
     */
    public function before(CodeWriterInterface $codewriter): void
    {
        $mode = strtolower(trim($this->expression));

        if ($mode === '' || $mode === 'default') {
            $mode = 'tales';
        }

        if ($mode !== 'php' && $mode !== 'tales') {
            throw new TemplateException(
                "Unsupported TALES mode '$mode'",
                $this->phpelement->getSourceFile(),
                $this->phpelement->getSourceLine()
            );
        }

        $this->oldMode = $codewriter->setTalesMode($mode);
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
        $codewriter->setTalesMode($this->oldMode);
    }
}
