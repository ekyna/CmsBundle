<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use Ekyna\Bundle\CmsBundle\Entity\SlideShow;

/**
 * Class Renderer
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Renderer implements RendererInterface
{
    /**
     * @var TypeRegistryInterface
     */
    private $registry;


    /**
     * Constructor.
     *
     * @param TypeRegistryInterface $registry
     */
    public function __construct(TypeRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns the registry.
     *
     * @return TypeRegistryInterface
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @inheritDoc
     */
    public function render(SlideShow $slideShow, array $options = [])
    {
        $dom = new \DOMDocument();

        $options = array_replace([
            'javascript' => true,
            'ui'         => true,
            'auto'       => true,
            'debug'      => false,
        ], $options);

        if (isset($options['id'])) {
            $id = $options['id'];
        } else {
            $id = 'cms-slide-' . ($slideShow->getId() ? $slideShow->getId() : 'example');
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

        $config = array_filter($options, function($key) {
            return in_array($key, ['ui', 'auto', 'debug']);
        }, ARRAY_FILTER_USE_KEY);

        if (!$options['javascript']) {
            $global->setAttribute('data-config', json_encode($config, JSON_FORCE_OBJECT));
        }

        $html = $dom->saveHTML();

        if ($options['javascript']) {
            $config['id'] = $id;
            $jsonConfig = json_encode($config, JSON_FORCE_OBJECT);

            $html .= <<<EOT
<script type="text/javascript">
    require(['ekyna-cms/slide-show'], function (SlideShow) {
        SlideShow.create($jsonConfig);
    });
</script>
EOT;
        }

        return $html;
    }
}
