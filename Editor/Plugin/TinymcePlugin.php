<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Entity\TinymceBlock;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;

/**
 * Class TinymcePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TinymcePlugin extends AbstractPlugin
{
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
     * {@inheritDoc}
     */
    public function create(array $datas = array())
    {
    	$block = new TinymceBlock();
        $defaultContent = array_key_exists('default_content', $this->config) ? $this->config['default_content'] : '';

        $block->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $block->setFallbackLocale($this->localeProvider->getFallbackLocale());

        $block->setHtml($defaultContent);

    	return $block;
    }

    /**
     * {@inheritDoc}
     */
    public function update(BlockInterface $block, array $datas = array())
    {
        /** @var TinymceBlock $block */
        if (array_key_exists('html', $datas)) {
            $block->translate(null, true);
            $block->setHtml($datas['html']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(BlockInterface $block) {}

    /**
     * {@inheritDoc}
     */
    public function getInnerHtml(BlockInterface $block)
    {
        /** @var TinymceBlock $block */
        return $block->getHtml();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return 'Ekyna\Bundle\CmsBundle\Entity\TinymceBlock';
    }
}
