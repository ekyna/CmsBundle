<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;

/**
 * AbstractPlugin.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;


    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

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
     * {@inheritDoc}
     */
    public function supports(BlockInterface $block)
    {
        return $block->getType() === $this->getType();
    }
}
