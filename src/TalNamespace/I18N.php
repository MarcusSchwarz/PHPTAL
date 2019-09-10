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
use PhpTal\TalNamespace\Attribute\TalNamespaceAttributeSurround;

/**
 * @internal
 */
final class I18N extends Builtin
{
    public function __construct()
    {
        parent::__construct('i18n', Builtin::NS_I18N);
        $this->addAttribute(new TalNamespaceAttributeContent('translate', 5));
        $this->addAttribute(new TalNamespaceAttributeSurround('name', 5));
        $this->addAttribute(new TalNamespaceAttributeSurround('attributes', 10));
        $this->addAttribute(new TalNamespaceAttributeSurround('domain', 3));
    }
}
