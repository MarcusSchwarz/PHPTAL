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

namespace PhpTal\TalNamespace;

use PhpTal\TalNamespace\Attribute\TalNamespaceAttributeSurround;

/**
 * @internal
 */
final class PHPTAL extends Builtin
{
    public function __construct()
    {
        parent::__construct('phptal', 'http://phptal.org/ns/phptal');
        $this->addAttribute(new TalNamespaceAttributeSurround('tales', -1));
        $this->addAttribute(new TalNamespaceAttributeSurround('debug', -2));
        $this->addAttribute(new TalNamespaceAttributeSurround('id', 7));
        $this->addAttribute(new TalNamespaceAttributeSurround('cache', -3));
    }
}
