<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\AbstractPlugin as BasePlugin;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;

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
     * Sets the localeProvider.
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
     * {@inheritdoc}
     */
    public function create(BlockInterface $block, array $data = [])
    {
        $block->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $block->setFallbackLocale($this->localeProvider->getFallbackLocale());
    }

    /**
     * {@inheritdoc}
     */
    public function remove(BlockInterface $block)
    {
        $block->setData([]);
        foreach ($block->getTranslations() as $blockTranslation) {
            $blockTranslation->setData([]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports(BlockInterface $block)
    {
        return $block->getType() === $this->getName();
    }
}
