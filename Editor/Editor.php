<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ekyna\Bundle\CmsBundle\Entity\Content;

/**
 * Editor.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Editor
{
    /**
     * @var PluginRegistry
     */
    private $registry;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var Content
     */
    private $content;

    /**
     * Constructor.
     * 
     * @param PluginRegistry $registry
     */
    public function __construct(PluginRegistry $registry, ObjectManager $manager)
    {
        $this->registry = $registry;
        $this->manager  = $manager;
    }

    /**
     * Initializes the content.
     * 
     * @param integer $id
     */
    public function initContent($id)
    {
        $repo = $this->manager->getRepository('EkynaCmsBundle:Content');

        if (null === $this->content = $repo->find($id)) {
            throw new \InvalidArgumentException('Content not found.');
        }

        return $this;
    }

    /**
     * Creates a block.
     * 
     * @param array $datas
     * 
     * @return array
     */
    public function createBlock(array $datas = array())
    {
        if (! array_key_exists('type', $datas)) {
            throw new \InvalidArgumentException('"type" field is mandatory.');
        }

        $plugin = $this->registry->get($datas['type']);
        $block = $plugin->create($datas);

        $this->updateBlockCoords($block, $datas);
        $this->content->addBlock($block);

        $this->manager->persist($this->content);
        $this->manager->flush();

        return array(
    	    'datas' => $block->getInitDatas(),
    	    'innerHtml' => $plugin->getInnerHtml($block),
        );
    }

    /**
     * Updates a block.
     * 
     * @param array $datas
     * 
     * @return array
     */
    public function updateBlock(array $datas = array())
    {
        $block = $this->findBlock($datas);

        $plugin = $this->registry->get($block->getType());
        $plugin->update($block, $datas);

        $this->updateBlockCoords($block, $datas);

        $this->manager->persist($block);
        $this->manager->flush();

        return array(
            'id' => $block->getId(),
            'innerHtml' => $plugin->getInnerHtml($block),
        );
    }

    /**
     * Removes blocks.
     * 
     * @param array $datas
     * 
     * @return array
     */
    public function removeBlocks(array $datas = array())
    {
        $removedIds = array();
        foreach($datas as $blockDatas) {
            $block = $this->findBlock($blockDatas);
    
            $plugin = $this->registry->get($block->getType());
            $plugin->remove($block);
    
            $removedIds[] = $block->getId();
            $this->content->removeBlock($block);
            $this->manager->remove($block);
        }
        $this->manager->persist($this->content);
        $this->manager->flush();

        return array(
            'ids' => $removedIds,
        );
    }

    /**
     * Updates the layout.
     * 
     * @param array $datas
     */
    public function updateLayout(array $datas = array())
    {
        foreach($datas as $coords) {
            $block = $this->findBlock($coords);
            $this->updateBlockCoords($block, $coords);
            $this->manager->persist($block);
        }
        $this->manager->flush();
    }

    /**
     * Updates coords of the given block.
     * 
     * @param BlockInterface $block
     * @param array          $datas
     */
    private function updateBlockCoords(BlockInterface $block, array $datas = array())
    {
        if (array_key_exists('row', $datas)) {
            $block->setRow($datas['row']);
        }
        if (array_key_exists('column', $datas)) {
            $block->setColumn($datas['column']);
        }
        if (array_key_exists('size', $datas)) {
            $block->setSize($datas['size']);
        }
    }

    /**
     * Finds a block.
     * 
     * @param array $datas
     * 
     * @return BlockInterface
     */
    private function findBlock(array $datas = array())
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
        if (! array_key_exists('id', $datas) || 0 >= ($blockId = intval($datas['id']))) {
            throw new \InvalidArgumentException('Block "id" is mandatory.');
        }
        $block = $this->manager
            ->getRepository('EkynaCmsBundle:AbstractBlock')
            ->findOneBy(array(
                'content' => $this->content,
                'name'    => null,
                'id'      => $blockId
            ));
        if (null === $block) {
            throw new \RuntimeException('Block not found.');
        }
        return $block;
    }
}
