<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\AbstractPlugin as BasePlugin;
use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class AbstractPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin extends BasePlugin implements PluginInterface
{
    const INVALID_DATA = 'ekyna_cms.block.invalid_data';

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;


    /**
     * Sets the locale provider.
     *
     * @param LocaleProviderInterface $localeProvider
     */
    public function setLocaleProvider(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * Returns the plugin config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function create(BlockInterface $block, array $data = [])
    {
        $block->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $block->setFallbackLocale($this->localeProvider->getFallbackLocale());
    }

    /**
     * @inheritdoc
     */
    public function remove(BlockInterface $block)
    {
        $block->unsetData();
        foreach ($block->getTranslations() as $blockTranslation) {
            $blockTranslation->unsetData();
        }
    }

    /**
     * @inheritdoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context)
    {

    }

    /**
     * @inheritDoc
     */
    public function render(BlockInterface $block, BlockView $view, AdapterInterface $adapter, array $options)
    {
        $view->widgets[] = $this->createWidget($block, $adapter, $options);
    }

    /**
     * @inheritdoc
     */
    public function createWidget(BlockInterface $block, AdapterInterface $adapter, array $options, $position = 0)
    {
        $options = array_replace([
            'editable' => false,
        ], $options);

        $view = new WidgetView();
        $attributes = $view->getAttributes()->addClass('cms-widget');

        if ($options['editable']) {
            $attributes
                ->setId('cms-widget-' . $block->getId())
                ->setData([
                    'id'       => $block->getId(),
                    'type'     => $this->getName(),
                    'position' => $position,
                    'actions'  => [
                        'edit' => true,
                    ],
                ]);
        }

        return $view;
    }

    /**
     * @inheritdoc
     */
    public function supports(BlockInterface $block)
    {
        return $block->getType() === $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/default';
    }
}
