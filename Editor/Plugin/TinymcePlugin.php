<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;

/**
 * Class TinymcePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TinymcePlugin extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function create(BlockInterface $block, array $data = [])
    {
        $defaultContent = array_key_exists('default_content', $this->config) ? $this->config['default_content'] : '';

        $block->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $block->setFallbackLocale($this->localeProvider->getFallbackLocale());

        // TODO $defaultContent
        $block->setData(array());

        return $block;
    }

    /**
     * {@inheritDoc}
     */
    public function update(BlockInterface $block, array $data = [])
    {
        if (array_key_exists('html', $data)) {
            $block->translate(null, true);
            $block->setData($data['html']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(BlockInterface $block)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render(BlockInterface $block)
    {
        // TODO
        return '<p>Fake tinymce render</p>';
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'ekyna_cms_tinymce';
    }
}
