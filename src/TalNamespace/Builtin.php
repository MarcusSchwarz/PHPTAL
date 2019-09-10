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

use PhpTal\Dom\Element;
use PhpTal\Php\Attribute;
use PhpTal\TalNamespace\Attribute\TalNamespaceAttribute;

/**
 * @internal
 */
abstract class Builtin extends TalNamespace
{

    public const NS_METAL = 'http://xml.zope.org/namespaces/metal';
    public const NS_TAL = 'http://xml.zope.org/namespaces/tal';
    public const NS_I18N = 'http://xml.zope.org/namespaces/i18n';
    public const NS_XML = 'http://www.w3.org/XML/1998/namespace';
    public const NS_XMLNS = 'http://www.w3.org/2000/xmlns/';
    public const NS_XHTML = 'http://www.w3.org/1999/xhtml';

    /**
     * @param \PhpTal\TalNamespace\Attribute\TalNamespaceAttribute $att
     * @param Element $tag
     * @param mixed $expression
     *
     * @return Attribute
     */
    public function createAttributeHandler(TalNamespaceAttribute $att, Element $tag, $expression): Attribute
    {
        $name = $att->getLocalName();

        // change define-macro to "define macro" and capitalize words
        $name = str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));

        // case is important when using autoload on case-sensitive filesystems
            $class = 'PhpTal\\Php\\Attribute\\'.strtoupper($this->getPrefix()).'\\'.$name;

        return new $class($tag, $expression);
    }
}
