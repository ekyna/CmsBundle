<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use DOMDocument;
use Ekyna\Bundle\CmsBundle\Entity\SlideShow;
use Ekyna\Bundle\CmsBundle\Repository\SlideShowRepositoryInterface;
use InvalidArgumentException;

use function is_string;

/**
 * Class SlideShowRenderer
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShowRenderer implements RendererInterface
{
    private TypeRegistryInterface        $registry;
    private SlideShowRepositoryInterface $repository;

    public function __construct(TypeRegistryInterface $registry, SlideShowRepositoryInterface $repository)
    {
        $this->registry = $registry;
        $this->repository = $repository;
    }

    /**
     * Returns the registry.
     */
    public function getRegistry(): TypeRegistryInterface
    {
        return $this->registry;
    }

    public function render(SlideShow $slideShow, array $options = []): string
    {
        $dom = new DOMDocument();

        $options = array_replace([
            'ui'         => true,
            'auto'       => true,
            'javascript' => false,
            'debug'      => false,
        ], $options);

        if (isset($options['id'])) {
            $id = $options['id'];
        } else {
            $id = 'cms-slide-' . ($slideShow->getId() ?: 'example');
        }

        $global = $dom->createElement('div');
        $global->setAttribute('class', 'cms-slide-show');
        $global->setAttribute('id', $id);

        $slides = $dom->createElement('div');
        $slides->setAttribute('class', 'cms-slides');

        foreach ($slideShow->getSlides() as $slide) {
            $type = $this->registry->get($slide->getType());

            $element = $dom->createElement('div');

            $type->render($slide, $element, $dom);

            $classes = explode(' ', $element->getAttribute('class'));
            if (!in_array('cms-slide', $classes)) {
                $classes[] = 'cms-slide';
                $element->setAttribute('class', implode(' ', $classes));
            }
            $element->setAttribute('data-type', $type->getName());

            $slides->appendChild($element);
        }

        $global->appendChild($slides);
        $dom->appendChild($global);

        $config = array_filter($options, function ($key) {
            return in_array($key, ['ui', 'auto', 'debug']);
        }, ARRAY_FILTER_USE_KEY);

        $config['id'] = $id;

        $jsonConfig = json_encode($config, JSON_FORCE_OBJECT);

        if (!$options['javascript']) {
            $global->setAttribute('data-config', $jsonConfig);
        }

        $html = $dom->saveHTML();

        if ($options['javascript']) {
            $html .= <<<EOT
<script type="text/javascript">require(['ekyna-cms/slide-show/slide-show'], function (SlideShow) { SlideShow.create($jsonConfig) });</script>
EOT;
        }

        return $html;
    }

    /**
     * Returns the slide show config.
     *
     * @param SlideShow|string $slideShowOrTag
     */
    public function renderSlideShow($slideShowOrTag, array $options = []): string
    {
        if (is_string($slideShowOrTag)) {
            $slideShowOrTag = $this->repository->findOneBy(['tag' => $slideShowOrTag]);
        }

        if (!$slideShowOrTag instanceof SlideShow) {
            throw new InvalidArgumentException('Expected tag or instance of ' . SlideShow::class);
        }

        return $this->render($slideShowOrTag, $options);
    }
}
