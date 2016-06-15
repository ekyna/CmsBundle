<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TinymcePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TinymcePlugin extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function create(BlockInterface $block, array $data = [])
    {
        //$defaultContent = array_key_exists('default_content', $this->config) ? $this->config['default_content'] : '';

        $block->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $block->setFallbackLocale($this->localeProvider->getFallbackLocale());

        // TODO $defaultContent
        $block->setData(array());
        $block->translate(null, true)->setData([
            'content' => '<p>Tinymce block default content.</p>'
        ]);;
    }

    /**
     * {@inheritDoc}
     */
    public function update(BlockInterface $block, Request $request)
    {
        if (!$request->isMethod('POST')) {
            throw new InvalidOperationException('Tinymce block plugin only supports POST request.');
        }

        $data = $request->request->get('data');
        if (!array_key_exists('content', $data)) {
            throw new InvalidOperationException('Invalid POST data.');
        }

        $block->translate(null, true)->setData([
            'content' => $data['content']
        ]);

        return null;
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
        $data = $block->translate(null, true)->getData();

        if (array_key_exists('content', $data)) {
            return $data['content'];
        }

        return 'Tinymce block no content';
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'ekyna_cms_tinymce';
    }

    /**
     * {@inheritDoc}
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/tinymce-plugin';
    }
}
