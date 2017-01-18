<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;

/**
 * Class BlockListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @todo resource (persistence) event subscriber ?
 */
class BlockListener
{
    /**
     * @var TagManager
     */
    private $tagManager;


    /**
     * Constructor.
     *
     * @param TagManager $tagManager
     */
    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    /**
     * Post update event handler.
     *
     * @param BlockInterface $block
     */
    public function postUpdate(BlockInterface $block)
    {
        $this->invalidateBlockContent($block);
    }

    /**
     * Post remove event handler.
     *
     * @param BlockInterface $block
     */
    public function postRemove(BlockInterface $block)
    {
        $this->invalidateBlockContent($block);
    }

    /**
     * Invalidate the block's content http cache tag.
     *
     * @param BlockInterface $block
     */
    private function invalidateBlockContent(BlockInterface $block)
    {
        /* TODO if (null !== $container = $block->getContainer()) {
            if (null !== $content = $container->getContent()) {
                $this->tagManager->invalidateTags($content->getEntityTag());
            }
        }*/
    }
}
