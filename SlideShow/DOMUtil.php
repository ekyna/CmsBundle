<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use DOMElement;

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
     * @param DOMElement      $element
     * @param string|string[] $classes
     */
    public static function addClass(DOMElement $element, $classes): void
    {
        if (!is_array($classes)) {
            $classes = [$classes];
        }

        $c = explode(' ', $element->getAttribute('class'));

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
     * @param DOMElement $element
     * @param string     $property
     * @param string     $value
     */
    public static function addStyle(DOMElement $element, string $property, string $value): void
    {
        $s = static::explodeStyles($element->getAttribute('style'));

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
    public static function explodeStyles(string $styles): array
    {
        $a = [];

        $s = explode(';', $styles);

        foreach ($s as $c) {
            if (empty($c)) {
                continue;
            }
            [$p, $v] = explode(':', $c);
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
    public static function implodeStyles(array $styles): string
    {
        $a = [];

        foreach ($styles as $p => $v) {
            $a[] = "$p:$v";
        }

        return implode(';', $a);
    }
}
