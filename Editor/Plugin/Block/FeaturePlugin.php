<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistryAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistryAwareTrait;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PropertyDefaults;
use Ekyna\Bundle\CmsBundle\Editor\View\AttributesInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\FeatureBlockType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class FeaturePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class FeaturePlugin extends AbstractPlugin implements PluginRegistryAwareInterface
{
    use PluginRegistryAwareTrait;

    public const NAME = 'ekyna_block_feature';

    private const DEFAULT_HTML_MAX_WIDTH  = '150px';
    private const DEFAULT_HTML_MARGIN_TOP = '20px';


    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct(array_replace([
            'image_filter' => 'cms_block_feature',
            'animations'   => PropertyDefaults::getDefaultAnimationChoices(),
        ], $config));
    }

    /**
     * @inheritDoc
     */
    public function create(BlockInterface $block, array $data = []): void
    {
        parent::create($block, $data);

        $this->getImagePlugin()->create($block, array_merge($data, [
            'max_width' => static::DEFAULT_HTML_MAX_WIDTH,
        ]));
        $this->getHtmlPlugin()->create($block, $data);

        $block->setData('animation', [
            'name'     => null,
            'offset'   => 120,
            'duration' => 400,
            'once'     => false,
        ]);
        $block->setData('html_max_width', static::DEFAULT_HTML_MAX_WIDTH);
        $block->setData('html_margin_top', static::DEFAULT_HTML_MARGIN_TOP);
    }

    /**
     * @inheritDoc
     */
    public function update(BlockInterface $block, Request $request, array $options = []): ?Response
    {
        $type = $request->get('widgetType');

        // Fallback to sub widgets if required
        if ($type === ImagePlugin::NAME) {
            return $this->getImagePlugin()->update($block, $request);
        } elseif ($type === TinymcePlugin::NAME) {
            return $this->getHtmlPlugin()->update($block, $request);
        }

        // Feature update modal
        $form = $this->formFactory->create(FeatureBlockType::class, $block, [
            'action'     => $this->urlGenerator->generate('admin_ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method'     => 'post',
            'attr'       => [
                'class' => 'form-horizontal',
            ],
            'animations' => $this->config['animations'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }

        return $this->createModalResponse('Modifier le bloc feature.', $form->createView());
    }

    /**
     * @inheritDoc
     */
    public function remove(BlockInterface $block): void
    {
        $this->getImagePlugin()->remove($block);
        $this->getHtmlPlugin()->remove($block);

        parent::remove($block);
    }

    /**
     * @inheritDoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context): void
    {
        $this->getImagePlugin()->validate($block, $context);
        $this->getHtmlPlugin()->validate($block, $context);
    }

    /**
     * @inheritDoc
     */
    public function render(BlockInterface $block, BlockView $view, AdapterInterface $adapter, array $options): void
    {
        $options = array_replace([
            'editable' => false,
        ], $options);

        $overrideAttributes = function (AttributesInterface $attributes, $type) use ($options) {
            $attributes->addClass('feature-' . $type);

            if ($options['editable']) {
                $attributes->setId($attributes->getId() . '-' . $type);
            }
        };

        $data = $block->getData();

        // Feature block view
        $attributes = $view->getAttributes();
        $attributes->addClass('cms-feature');

        // Set editable
        $attributes->setData(['actions' => ['edit' => true]]);

        // Animation
        $hasAnim = false;
        if (isset($data['animation'])) {
            $animData = $data['animation'] ?? [];
            if (isset($animData['name']) && isset($this->config['animations'][$animData['name']])) {
                $hasAnim = true;
                $attributes->setExtra('data-aos', (string)$animData['name']);
                foreach (['duration', 'offset', 'once'] as $prop) {
                    if (isset($animData[$prop]) && $animData[$prop]) {
                        $attributes->setExtra('data-aos-' . $prop, (string)$animData[$prop]);
                    }
                }
            }
        }

        // Image widget view
        $widget = $this
            ->getImagePlugin()
            ->createWidget($block, $adapter, array_replace($options, [
                'filter'    => $this->config['image_filter'],
                'animation' => !$hasAnim,
            ]));

        $overrideAttributes($widget->getAttributes(), 'image');
        $view->widgets[] = $widget;

        // Html widget view
        $widget = $this
            ->getHtmlPlugin()
            ->createWidget($block, $adapter, $options, 1);
        $overrideAttributes($widget->getAttributes(), 'html');
        if (isset($data['html_max_width'])) {
            $widget->getAttributes()->addStyle('max-width', $data['html_max_width']);
        } else {
            $widget->getAttributes()->addStyle('max-width', static::DEFAULT_HTML_MAX_WIDTH);
        }
        if (isset($data['html_margin_top'])) {
            $widget->getAttributes()->addStyle('margin-top', $data['html_margin_top']);
        } else {
            $widget->getAttributes()->addStyle('margin-top', static::DEFAULT_HTML_MARGIN_TOP);
        }
        $view->widgets[] = $widget;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Feature';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath(): string
    {
        return 'ekyna-cms/editor/plugin/block/feature';
    }

    /**
     * Returns the image plugin.
     *
     * @return PluginInterface
     */
    protected function getImagePlugin(): PluginInterface
    {
        return $this->getBlockPlugin(ImagePlugin::NAME);
    }

    /**
     * Returns the html plugin.
     *
     * @return PluginInterface
     */
    protected function getHtmlPlugin(): PluginInterface
    {
        return $this->getBlockPlugin(TinymcePlugin::NAME);
    }
}
