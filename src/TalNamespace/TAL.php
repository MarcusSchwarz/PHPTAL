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

use PhpTal\TalNamespace\Attribute\TalNamespaceAttributeContent;
use PhpTal\TalNamespace\Attribute\TalNamespaceAttributeReplace;
use PhpTal\TalNamespace\Attribute\TalNamespaceAttributeSurround;

/**
 * @interal
 */
final class TAL extends Builtin
{
    public function __construct()
    {
        parent::__construct('tal', Builtin::NS_TAL);
        $this->addAttribute(new TalNamespaceAttributeSurround('define', 4));
        $this->addAttribute(new TalNamespaceAttributeSurround('condition', 6));
        $this->addAttribute(new TalNamespaceAttributeSurround('repeat', 8));
        $this->addAttribute(new TalNamespaceAttributeContent('content', 11));
        $this->addAttribute(new TalNamespaceAttributeReplace('replace', 9));
        $this->addAttribute(new TalNamespaceAttributeSurround('attributes', 9));
        $this->addAttribute(new TalNamespaceAttributeSurround('omit-tag', 0));
        $this->addAttribute(new TalNamespaceAttributeSurround('comment', 12));
        $this->addAttribute(new TalNamespaceAttributeSurround('on-error', 2));
    }
}
