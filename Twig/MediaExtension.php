<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\CmsBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class MediaExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaExtension extends AbstractExtension
{
    /**
     * @var CacheManager
     */
    private $manager;


    /**
     * Constructor.
     *
     * @param CacheManager $manager
     */
    public function __construct(CacheManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('cms_image', [$this, 'renderImage'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Renders the image as responsive.
     *
     * @param MediaInterface $image
     * @param array          $columns
     * @param array          $attr
     *
     * @return string
     */
    public function renderImage(MediaInterface $image, array $columns, array $attr = []): string
    {
        if (!$image->getType() === MediaTypes::IMAGE) {
            throw new InvalidArgumentException("Expected 'image' media type.");
        }

        $map = [];
        $col = 12;
        foreach (array_reverse(Bootstrap3Adapter::getDevices(), true) as $d => $config) {
            $col = isset($columns[$d]) ? $columns[$d] : $col;
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
