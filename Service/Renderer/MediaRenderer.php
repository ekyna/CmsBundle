<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use InvalidArgumentException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use function array_keys;
use function array_map;
use function array_reverse;
use function implode;
use function intval;
use function sprintf;
use function trim;

/**
 * Class MediaRenderer
 * @package Ekyna\Bundle\CmsBundle\Service\Renderer
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaRenderer
{
    private CacheManager $manager;

    public function __construct(CacheManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Renders the image as responsive.
     */
    public function renderImage(MediaInterface $image, array $columns, array $attr = []): string
    {
        if ($image->getType() !== MediaTypes::IMAGE) {
            throw new InvalidArgumentException("Expected 'image' media type.");
        }

        $map = [];
        $col = 12;
        foreach (array_reverse(Bootstrap3Adapter::getDevices(), true) as $d => $config) {
            $col = $columns[$d] ?? $col;
            $count = 12 / $col;
            $map[sprintf('col_%s_%d', $d, $col)] = [
                'max'   => $config['max'],
                'width' => intval(($config['width'] - ($count - 1) * 30) / $count),
            ];
        }

        $url = null;
        $srcSet = [];
        $sizes = [];
        foreach ($map as $filter => $config) {
            $url = $this->manager->getBrowserPath($image->getPath(), $filter);
            $srcSet[] = $url . ' ' . $config['width'] . 'w';
            $sizes[] = trim(
                ($config['max'] ? '(max-width:' . $config['max'] . 'px)' : '') . ' ' . $config['width'] . 'px'
            );
        }

        if (!isset($attr['alt'])) {
            $attr['alt'] = $image->getTitle();
        }

        $attr['src'] = $url;
        $attr['srcset'] = implode(', ', $srcSet);
        $attr['sizes'] = implode(', ', $sizes);

        $attributes = implode(' ', array_map(function($key, $value) {
            return sprintf('%s="%s"', $key, $value);
        }, array_keys($attr), $attr));

        /** @noinspection HtmlRequiredAltAttribute */
        return "<img $attributes>";
    }
}
