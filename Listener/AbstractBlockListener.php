<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;

/**
 * Class AbstractBlockListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AbstractBlockListener
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
        if (null !== $content = $block->getContent()) {
            $this->tagManager->invalidateTags($content->getEntityTag());
        }
    }
}
