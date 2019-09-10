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
use PhpTal\Exception\ConfigurationException;
use PhpTal\Php\Attribute;
use PhpTal\TalNamespace\Attribute\TalNamespaceAttribute;

/**
 * @see \PhpTal\TalNamespace\Attribute\TalNamespaceAttribute
 * @package PHPTAL
 */
abstract class TalNamespace
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $namespace_uri;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * TalNamespace constructor.
     *
     * @param string $prefix
     * @param string $namespace_uri
     * @throws ConfigurationException
     */
    public function __construct(string $prefix, string $namespace_uri)
    {
        if (empty($namespace_uri) || empty($prefix)) {
            throw new ConfigurationException("Can't create namespace with empty prefix or namespace URI");
        }

        $this->attributes = [];
        $this->prefix = $prefix;
        $this->namespace_uri = $namespace_uri;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getNamespaceURI(): string
    {
        return $this->namespace_uri;
    }

    /**
     * @param TalNamespaceAttribute $attribute
     *
     * @return void
     */
    public function addAttribute(TalNamespaceAttribute $attribute): void
    {
        $attribute->setNamespace($this);
        $this->attributes[strtolower($attribute->getLocalName())] = $attribute;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param TalNamespaceAttribute $att
     * @param Element $tag
     * @param mixed $expression
     *
     * @return Attribute
     */
    abstract public function createAttributeHandler(
        TalNamespaceAttribute $att,
        Element $tag,
        $expression
    ): Attribute;
}
