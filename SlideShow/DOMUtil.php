<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow;

/**
 * Class Util
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class DOMUtil
{
    /**
     * Adds the class to the dom element.
     *
     * @param \DOMElement     $element
     * @param string|string[] $classes
     */
    static public function addClass(\DOMElement $element, $classes)
    {
        if (!is_array($classes)) {
            $classes = [$classes];
        }

        $c = explode(' ', (string)$element->getAttribute('class'));

        foreach ($classes as $n) {
            if (empty($n)) {
                continue;
            }
            if (!in_array($n, $c, true)) {
                $c[] = $n;
            }
        }

        $element->setAttribute('class', trim(implode(' ', $c)));
    }

    /**
     * Adds the style to the dom element.
     *
     * @param \DOMElement $element
     * @param string      $property
     * @param string      $value
     */
    static public function addStyle(\DOMElement $element, $property, $value)
    {
        $s = static::explodeStyles((string)$element->getAttribute('style'));

        $s[$property] = $value;

        $element->setAttribute('style', static::implodeStyles($s));
    }

    /**
     * Explodes the style attribute.
     *
     * @param string $styles
     *
     * @return array
     */
    static public function explodeStyles($styles)
    {
        $a = [];

        $s = explode(';', $styles);

        foreach ($s as $c) {
            if (empty($c)) continue;
            list($p, $v) = explode(':', $c);
            $a[$p] = $v;
        }

        return $a;
    }

    /**
     * Implodes the style attribute.
     *
     * @param array $styles
     *
     * @return string
     */
    static public function implodeStyles(array $styles)
    {
        $a = [];

        foreach ($styles as $p => $v) {
            $a[] = "$p:$v";
        }

        return implode(';', $a);
    }
}
