<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\AbstractPlugin as BasePlugin;
use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class AbstractPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin extends BasePlugin implements PluginInterface
{
    public const INVALID_DATA = 'ekyna_cms.block.invalid_data';

    protected LocaleProviderInterface $localeProvider;


    /**
     * Sets the locale provider.
     *
     * @param LocaleProviderInterface $localeProvider
     */
    public function setLocaleProvider(LocaleProviderInterface $localeProvider): void
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * @inheritDoc
     */
    public function create(BlockInterface $block, array $data = []): void
    {
        $block->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $block->setFallbackLocale($this->localeProvider->getFallbackLocale());
    }

    /**
     * @inheritDoc
     */
    public function remove(BlockInterface $block): void
    {
        $block->unsetData();
        foreach ($block->getTranslations() as $blockTranslation) {
            $blockTranslation->unsetData();
        }
    }

    /**
     * @inheritDoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context): void
    {
    }

    /**
     * @inheritDoc
     */
    public function render(BlockInterface $block, BlockView $view, AdapterInterface $adapter, array $options): void
    {
        $view->widgets[] = $this->createWidget($block, $adapter, $options);
    }

    /**
     * @inheritDoc
     */
    public function createWidget(
        BlockInterface $block,
        AdapterInterface $adapter,
        array $options,
        int $position = 0
    ): WidgetView {
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
     * @inheritDoc
     */
    public function supports(BlockInterface $block): bool
    {
        return $block->getType() === $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath(): string
    {
        return 'ekyna-cms/editor/plugin/block/default';
    }
}
