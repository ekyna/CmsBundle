<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Doctrine\Common\Persistence\ObjectManager;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Editor
 * @package Ekyna\Bundle\CmsBundle\Editor
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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ContentInterface
     */
    private $content;

    /**
     * Constructor.
     * 
     * @param PluginRegistry $registry
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     */
    public function __construct(PluginRegistry $registry, ObjectManager $manager, ValidatorInterface $validator)
    {
        $this->registry = $registry;
        $this->manager  = $manager;
        $this->validator = $validator;
    }

    /**
     * Initializes the content.
     * 
     * @param integer $id
     * @throws \InvalidArgumentException
     * @return Editor
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
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createBlock(array $datas = array())
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
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
     * @return array
     */
    public function updateBlock(array $datas = array())
    {
        $block = $this->findBlock($datas);

        $plugin = $this->registry->get($block->getType());
        $plugin->update($block, $datas);

        if (null !== $this->content) {
            $this->updateBlockCoords($block, $datas);
        }

        // TODO content validation

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
     * @return array
     * @throws \InvalidArgumentException
     */
    public function removeBlocks(array $datas = array())
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
        $removedIds = array();
        foreach($datas as $blockDatas) {
            $block = $this->findBlock($blockDatas);
    
            $plugin = $this->registry->get($block->getType());
            $plugin->remove($block);
    
            $removedIds[] = $block->getId();
            $this->content->removeBlock($block);
            $this->manager->remove($block);
        }

        // TODO content validation

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
     * @throws \InvalidArgumentException
     */
    public function updateLayout(array $datas = array())
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
        foreach($datas as $coords) {
            $block = $this->findBlock($coords);
            $this->updateBlockCoords($block, $coords);
            $this->manager->persist($block);
        }

        // TODO content validation

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
     * @return BlockInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function findBlock(array $datas = array())
    {
        if (! array_key_exists('id', $datas) || 0 >= ($blockId = intval($datas['id']))) {
            throw new \InvalidArgumentException('Block "id" is mandatory.');
        }
        $parameters = array('id' => $blockId);
        if (null !== $this->content) {
            $parameters['content'] = $this->content;
        }
        $block = $this->manager
            ->getRepository('EkynaCmsBundle:AbstractBlock')
            ->findOneBy($parameters)
        ;
        if (null === $block) {
            throw new \RuntimeException('Block not found.');
        }
        return $block;
    }
}
