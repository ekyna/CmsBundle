<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistryAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistryAwareTrait;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\Attributes;
use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\FeatureBlockType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class FeaturePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class FeaturePlugin extends AbstractPlugin implements PluginRegistryAwareInterface
{
    use PluginRegistryAwareTrait;

    const NAME = 'ekyna_block_feature';


    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(
        array $config
    ) {
        parent::__construct(array_replace([
            'image_filter' => 'cms_block_feature',
            'styles'       => static::getDefaultStyleChoices(),
            'animations'   => static::getDefaultAnimationChoices(),
        ], $config));
    }

    /**
     * @inheritdoc
     */
    public function create(BlockInterface $block, array $data = [])
    {
        parent::create($block, $data);

        $this->getImagePlugin()->create($block, $data);
        $this->getHtmlPlugin()->create($block, $data);

        $block->setData('style', null);
        $block->setData('animation', null);
    }

    /**
     * @inheritdoc
     */
    public function update(BlockInterface $block, Request $request, array $options = [])
    {
        $type = $request->get('widgetType');

        // Fallback to sub widgets if required
        if ($type === ImagePlugin::NAME) {
            return $this->getImagePlugin()->update($block, $request, [
                'style_choices' => null,
                'with_hover'    => true,
            ]);
        } elseif ($type === TinymcePlugin::NAME) {
            return $this->getHtmlPlugin()->update($block, $request);
        }

        // Feature update modal
        $form = $this->formFactory->create(FeatureBlockType::class, $block->getData(), [
            'action'            => $this->urlGenerator->generate('ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method'            => 'post',
            'attr'              => [
                'class' => 'form-horizontal',
            ],
            'style_choices'     => $this->config['styles'],
            'animation_choices' => $this->config['animations'],
        ]);

        if ($request->getMethod() == 'POST' && $form->handleRequest($request) && $form->isValid()) {
            $data = $form->getData();

            $block->setData('style', $data['style']);
            $block->setData('animation', $data['animation']);

            return null;
        }

        return $this->createModal('Modifier le bloc feature.', $form->createView());
    }

    /**
     * @inheritdoc
     */
    public function remove(BlockInterface $block)
    {
        $this->getImagePlugin()->remove($block);
        $this->getHtmlPlugin()->remove($block);

        parent::remove($block);
    }

    /**
     * @inheritdoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context)
    {
        $this->getImagePlugin()->validate($block, $context);
        $this->getHtmlPlugin()->validate($block, $context);
    }

    /**
     * @inheritdoc
     */
    public function render(BlockInterface $block, BlockView $view, array $options)
    {
        $options = array_replace([
            'editable' => false,
        ], $options);

        $overrideAttributes = function (Attributes $attributes, $type) use ($options) {
            $attributes->addClass('feature-' . $type);

            if ($options['editable']) {
                $attributes->setId($attributes->getId() . '-' . $type);
            }
        };

        // Image widget view
        $widget = $this
            ->getImagePlugin()
            ->createWidget($block, array_replace($options, [
                'filter' => $this->config['image_filter'],
            ]), 0);
        $overrideAttributes($widget->getAttributes(), 'image');
        $view->widgets[] = $widget;

        // Html widget view
        $widget = $this
            ->getHtmlPlugin()
            ->createWidget($block, $options, 1);
        $overrideAttributes($widget->getAttributes(), 'html');
        $view->widgets[] = $widget;

        // Feature block view
        $data = $block->getData();
        $attributes = $view->getAttributes();
        $attributes->addClass('cms-feature');

        // Set editable
        $attributes->setData(['actions' => ['edit' => true]]);

        // Style
        if (isset($data['style']) && isset($this->config['styles'][$data['style']])) {
            $attributes->addClass($data['style']);
        }

        // Animation
        if (isset($data['animation']) && isset($this->config['animations'][$data['animation']])) {
            $attributes->setExtra('data-aos', $data['animation']);
        }
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Feature';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @inheritdoc
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/feature';
    }

    /**
     * Returns the image plugin.
     *
     * @return PluginInterface
     */
    protected function getImagePlugin()
    {
        return $this->getBlockPlugin(ImagePlugin::NAME);
    }

    /**
     * Returns the html plugin.
     *
     * @return PluginInterface
     */
    protected function getHtmlPlugin()
    {
        return $this->getBlockPlugin(TinymcePlugin::NAME);
    }

    /**
     * Returns the default choices.
     *
     * @return array
     */
    static public function getDefaultStyleChoices()
    {
        return [
            'rounded' => 'Rounded',
        ];
    }

    /**
     * Returns the default choices.
     *
     * @return array
     */
    static public function getDefaultAnimationChoices()
    {
        return [
            // Fade
            'fade'            => 'Fade',
            'fade-up'         => 'Fade up',
            'fade-down'       => 'Fade down',
            'fade-left'       => 'Fade left',
            'fade-right'      => 'Fade right',
            'fade-up-right'   => 'Fade up right',
            'fade-up-left'    => 'Fade up left',
            'fade-down-right' => 'Fade down right',
            'fade-down-left'  => 'Fade down left',
            // Flip
            'flip-up'         => 'Flip up',
            'flip-down'       => 'Flip down',
            'flip-left'       => 'Flip left',
            'flip-right'      => 'Flip right',
            // Slide
            'slide-up'        => 'Slide up',
            'slide-down'      => 'Slide down',
            'slide-left'      => 'Slide left',
            'slide-right'     => 'Slide right',
            // Zoom
            'zoom-in'         => 'Zoom in',
            'zoom-in-up'      => 'Zoom in up',
            'zoom-in-down'    => 'Zoom in down',
            'zoom-in-left'    => 'Zoom in left',
            'zoom-in-right'   => 'Zoom in right',
            'zoom-out'        => 'Zoom out',
            'zoom-out-up'     => 'Zoom out up',
            'zoom-out-down'   => 'Zoom out down',
            'zoom-out-left'   => 'Zoom out left',
            'zoom-out-right'  => 'Zoom out right',
        ];
    }
}
