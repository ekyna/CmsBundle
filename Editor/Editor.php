<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Doctrine\ORM\EntityManager;
use Ekyna\Bundle\CmsBundle\Entity\Content;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Symfony\Component\Security\Core\SecurityContext;
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
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var string
     */
    private $defaultBlockType;

    /**
     * @var ContentInterface
     */
    private $content;

    /**
     * @var bool
     */
    private $enabled;


    /**
     * Constructor.
     *
     * @param PluginRegistry         $registry
     * @param EntityManager          $manager
     * @param ValidatorInterface     $validator
     * @param SecurityContext        $securityContext
     * @param string                 $defaultBlockType
     */
    public function __construct(
        PluginRegistry     $registry,
        EntityManager      $manager,
        ValidatorInterface $validator,
        SecurityContext    $securityContext,
        $defaultBlockType  = 'tinymce'
    ) {
        $this->registry         = $registry;
        $this->manager          = $manager;
        $this->validator        = $validator;
        $this->securityContext  = $securityContext;
        $this->defaultBlockType = $defaultBlockType;
    }

    /**
     * Returns the enabled.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the enabled.
     *
     * @param boolean $enabled
     * @return Editor
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Creates and returns a "default" Content for the given subject.
     *
     * @param ContentSubjectInterface $subject
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface
     */
    public function createDefaultContent(ContentSubjectInterface $subject)
    {
        $block = $this->createDefaultBlock($this->defaultBlockType);

        $content = new Content();
        $content->addBlock($block);

        $subject->setContent($content);

        $this->manager->persist($content);
        $this->manager->persist($subject);
        $this->manager->flush();

        return $content;
    }

    /**
     * Creates a default block.
     *
     * @param string $type
     * @param array $data
     *
     * @return BlockInterface
     */
    private function createDefaultBlock($type, array $data = array())
    {
        $plugin = $this->registry->get($type);

        return $plugin->create($data);
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
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createBlock(array $data = array())
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
        if (! array_key_exists('type', $data)) {
            throw new \InvalidArgumentException('"type" field is mandatory.');
        }

        $plugin = $this->registry->get($data['type']);
        $block = $plugin->create($data);

        $this->updateBlockCoordinates($block, $data);
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
     * @param array $data
     * @return array
     */
    public function updateBlock(array $data = array())
    {
        $block = $this->findBlock($data);

        $plugin = $this->registry->get($block->getType());
        $plugin->update($block, $data);

        if (null !== $this->content) {
            $this->updateBlockCoordinates($block, $data);

            $this->content->setUpdatedAt(new \DateTime());
            $this->manager->persist($this->content);
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
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public function removeBlocks(array $data = array())
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }

        // Don't remove if there is only one block
        if (1 == $this->content->getBlocks()->count()) {
            return array();
        }

        $removedIds = array();
        foreach($data as $blockDatas) {
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
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function updateLayout(array $data = array())
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
        foreach($data as $coordinates) {
            $block = $this->findBlock($coordinates);
            $this->updateBlockCoordinates($block, $coordinates);
            $this->manager->persist($block);
        }

        // TODO content validation

        $this->manager->flush();
    }

    /**
     * Updates coordinates of the given block.
     *
     * @param BlockInterface $block
     * @param array          $coordinates
     */
    private function updateBlockCoordinates(BlockInterface $block, array $coordinates = array())
    {
        if (array_key_exists('row', $coordinates)) {
            $block->setRow($coordinates['row']);
        }
        if (array_key_exists('column', $coordinates)) {
            $block->setColumn($coordinates['column']);
        }
        if (array_key_exists('size', $coordinates)) {
            $block->setSize($coordinates['size']);
        }
    }

    /**
     * Finds a block.
     *
     * @param array $data
     * @return BlockInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function findBlock(array $data = array())
    {
        if (! array_key_exists('id', $data) || 0 >= ($blockId = intval($data['id']))) {
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

    /**
     * Finds a block by name or creates if not exists.
     *
     * @param string $name the block name
     * @param string $type the block type
     * @param array $data the block datas
     *
     * @return BlockInterface
     */
    public function findBlockByName($name, $type = null, array $data = array())
    {
        if (null === $type) {
            $type = $this->defaultBlockType;
        }

        $repository = $this->manager->getRepository('Ekyna\Bundle\CmsBundle\Entity\AbstractBlock');
        if (null === $block = $repository->findOneBy(array('name' => $name, 'content' => null))) {
            $block = $this->createDefaultBlock($type, $data);
            $block->setName($name);

            $this->manager->persist($block);
            $this->manager->flush($block);
        } else {
            // TODO test block type ?
        }

        return $block;
    }
}
