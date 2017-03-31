<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistryAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistryAwareTrait;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PropertyDefaults;
use Ekyna\Bundle\CmsBundle\Editor\View\AttributesInterface;
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
            'animations'   => PropertyDefaults::getDefaultAnimationChoices(),
        ], $config));
    }

    /**
     * @inheritdoc
     */
    public function create(BlockInterface $block, array $data = [])
    {
        parent::create($block, $data);

        $this->getImagePlugin()->create($block, array_merge($data, [
            'max_width' => '150px',
        ]));
        $this->getHtmlPlugin()->create($block, $data);

        $block->setData('animation', [
            'name'     => null,
            'offset'   => 120,
            'duration' => 400,
            'once'     => false,
        ]);
        $block->setData('html_max_width', '150px');
    }

    /**
     * @inheritdoc
     */
    public function update(BlockInterface $block, Request $request, array $options = [])
    {
        $type = $request->get('widgetType');

        // Fallback to sub widgets if required
        if ($type === ImagePlugin::NAME) {
            return $this->getImagePlugin()->update($block, $request);
        } elseif ($type === TinymcePlugin::NAME) {
            return $this->getHtmlPlugin()->update($block, $request);
        }

        // Feature update modal
        $form = $this->formFactory->create(FeatureBlockType::class, $block->getData(), [
            'action'     => $this->urlGenerator->generate('ekyna_cms_editor_block_edit', [
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
            $data = $form->getData();

            $block->setData($data);

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
        // TODO removed undefined data indexes

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
        $animData = $data['animation'];
        if (isset($animData['name']) && isset($this->config['animations'][$animData['name']])) {
            $hasAnim = true;
            $attributes->setExtra('data-aos', $animData['name']);
            foreach (['duration', 'offset', 'once'] as $prop) {
                if (isset($animData[$prop]) && $animData[$prop]) {
                    $attributes->setExtra('data-aos-' . $prop, $animData[$prop]);
                }
            }
        }

        // Image widget view
        $widget = $this
            ->getImagePlugin()
            ->createWidget($block, array_replace($options, [
                'filter'    => $this->config['image_filter'],
                'animation' => !$hasAnim,
            ]), 0);
        $overrideAttributes($widget->getAttributes(), 'image');
        $view->widgets[] = $widget;

        // Html widget view
        $widget = $this
            ->getHtmlPlugin()
            ->createWidget($block, $options, 1);
        $overrideAttributes($widget->getAttributes(), 'html');
        if (isset($data['html_max_width'])) {
            $widget->getAttributes()->addStyle('max-width', $data['html_max_width']);
        }
        $view->widgets[] = $widget;
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
}
