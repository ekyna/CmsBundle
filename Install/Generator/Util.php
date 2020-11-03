<?php

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Symfony\Component\Routing\Route;

/**
 * Class Util
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Util
{
    /**
     * Builds the path.
     *
     * @param string $path
     * @param array  $defaults
     *
     * @return string
     */
    public static function buildPath(string $path, array $defaults): string
    {
        $built   = '';

        preg_match_all('~/?[^/]+~', $path, $parts, PREG_SET_ORDER);
        foreach ($parts as $part) {
            if (preg_match_all('~{[^}]+}~', $part[0], $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE)) {
                $pos       = 0;
                $buildPart = '';

                foreach ($matches[0] as $match) {
                    if (0 < $match[1]) {
                        $buildPart .= substr($part[0], $pos, $match[1] - $pos);
                    }

                    $param = trim($match[0], '{}');
                    if (!array_key_exists($param, $defaults)) {
                        $buildPart .= $match[0];
                    } elseif (!empty($defaults[$param])) {
                        $buildPart .= $defaults[$param];
                    }

                    $pos = $match[1] + strlen($match[0]);
                }

                if ('/' !== $buildPart) {
                    $built .= $buildPart;
                }
            } else {
                $built .= $part[0];
            }
        }

        return $built;
    }

    /**
     * Returns whether the given route has at least one parameter without default value.
     *
     * @param Route $route
     *
     * @return bool
     */
    public static function isDynamic(Route $route)
    {
        $path     = $route->getPath();
        $defaults = $route->getDefaults();

        if (preg_match_all('~{[^}]+}~', $path, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $param = trim($match[0], '{}');
                if (!array_key_exists($param, $defaults)) {
                    return true;
                }
            }
        }

        return false;
    }
}
