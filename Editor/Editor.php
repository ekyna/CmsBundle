<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Doctrine\ORM\EntityManager;
use Ekyna\Bundle\CmsBundle\Entity;
use Ekyna\Bundle\CmsBundle\Model;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Editor
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Editor
{
    /**
     * @var PluginRegistry
     */
    private $pluginRegistry;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var string
     */
    private $defaultBlockType;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var Manager\BlockManager
     */
    private $blockManager;

    /**
     * @var Manager\RowManager
     */
    private $rowManager;


    /**
     * Constructor.
     *
     * @param PluginRegistry     $pluginRegistry
     * @param EntityManager      $manager
     * @param ValidatorInterface $validator
     * @param string             $defaultBlockType
     */
    public function __construct(
        PluginRegistry $pluginRegistry,
        EntityManager $manager,
        ValidatorInterface $validator,
        $defaultBlockType = 'ekyna_cms_tinymce'
    ) {
        $this->pluginRegistry = $pluginRegistry;
        $this->manager = $manager;
        $this->validator = $validator;
        $this->defaultBlockType = $defaultBlockType;
    }

    /**
     * Returns the block manager.
     *
     * @return Manager\BlockManager
     */
    public function getBlockManager()
    {
        if (null === $this->blockManager) {
            $this->blockManager = new Manager\BlockManager($this->pluginRegistry);
        }

        return $this->blockManager;
    }

    /**
     * Returns the row manager.
     *
     * @return Manager\RowManager
     */
    public function getRowManager()
    {
        if (null === $this->rowManager) {
            $this->rowManager = new Manager\RowManager();
        }

        return $this->rowManager;
    }

    /**
     * Sets the enabled.
     *
     * @param bool $enabled
     *
     * @return Editor
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;

        return $this;
    }

    /**
     * Returns the enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the plugin by name.
     *
     * @param string $name
     *
     * @return Plugin\PluginInterface
     */
    public function getPluginByName($name)
    {
        return $this->pluginRegistry->get($name);
    }

    /**
     * Creates a default content for the given subject.
     *
     * @param Model\ContentSubjectInterface $subject
     *
     * @return Model\ContentInterface
     */
    public function createDefaultContent(Model\ContentSubjectInterface $subject)
    {
        $content = new Entity\Content();
        $this->createDefaultContainer([], $content);

        $subject->setContent($content);

//        TODO (in controller)
        $this->manager->persist($content);
        $this->manager->persist($subject);
        $this->manager->flush();

        return $content;
    }

    /**
     * Creates a default container.
     *
     * @param array                  $data
     * @param Model\ContentInterface $content
     *
     * @return Model\ContainerInterface
     */
    public function createDefaultContainer(array $data = [], Model\ContentInterface $content = null)
    {
        $container = new Entity\Container();

        if (null !== $content) {
            $content->addContainer($container);
        }

        // TODO $data

        $this->createDefaultRow([], $container);

        return $container;
    }

    /**
     * Creates a default row.
     *
     * @param array                    $data
     * @param Model\ContainerInterface $container
     *
     * @return Model\RowInterface
     */
    public function createDefaultRow(array $data = [], Model\ContainerInterface $container = null)
    {
        $row = new Entity\Row();

        if (null !== $container) {
            $container->addRow($row);
        }

        // TODO $data

        $this->createDefaultBlock(null, [], $row);

        return $container;
    }

    /**
     * Creates a default block.
     *
     * @param string             $type
     * @param array              $data
     * @param Model\RowInterface $row
     *
     * @return Model\BlockInterface
     */
    public function createDefaultBlock(string $type = null, array $data = [], Model\RowInterface $row = null)
    {
        $block = $this->getBlockManager()->create($row, $type, $data);
        if ($row) {
            $this->getRowManager()->fixLayout($row);
        }

        return $block;
    }


    /**
     * Creates a block.
     *
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    /*public function createBlock(array $data = [])
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
        if (!array_key_exists('type', $data)) {
            throw new \InvalidArgumentException('"type" field is mandatory.');
        }

        $block = new Entity\Block();
        $block->setType($data['type']);

        $plugin = $this->pluginRegistry->get($data['type']);
        $block = $plugin->create($block, $data);

        $this->updateBlockCoordinates($block, $data);
        $this->content->addBlock($block);

//        TODO (in controller)
//        $this->manager->persist($this->content);
//        $this->manager->flush();

        return [
            'datas'     => $block->getInitDatas(),
            'innerHtml' => $plugin->getInnerHtml($block),
        ];
    }*/

    /**
     * Updates a block.
     *
     * @param array $data
     *
     * @return array
     */
    /*public function updateBlock(array $data = [])
    {
        $block = $this->findBlock($data);

        $plugin = $this->pluginRegistry->get($block->getType());
        $plugin->update($block, $data);

        if (null !== $this->content) {
            $this->updateBlockCoordinates($block, $data);

            $this->content->setUpdatedAt(new \DateTime());
            $this->manager->persist($this->content);
        }

        // TODO content validation

//        TODO (in controller)
//        $this->manager->persist($block);
//        $this->manager->flush();

        return [
            'id'        => $block->getId(),
            'innerHtml' => $plugin->getInnerHtml($block),
        ];
    }*/

    /**
     * Removes blocks.
     *
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    /*public function removeBlocks(array $data = [])
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }

        // Don't remove if there is only one block
        if (1 == $this->content->getBlocks()->count()) {
            return [];
        }

        $removedIds = [];
        foreach ($data as $blockData) {
            $block = $this->findBlock($blockData);

            $plugin = $this->pluginRegistry->get($block->getType());
            $plugin->remove($block);

            $removedIds[] = $block->getId();
            $this->content->removeBlock($block);
            $this->manager->remove($block);
        }

        // TODO content validation

//        TODO (in controller)
//        $this->manager->persist($this->content);
//        $this->manager->flush();

        return [
            'ids' => $removedIds,
        ];
    }*/

    /**
     * Updates the layout.
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     */
    /*public function updateLayout(array $data = [])
    {
        if (null === $this->content) {
            throw new \InvalidArgumentException('No Content selected.');
        }
        foreach ($data as $coordinates) {
            $block = $this->findBlock($coordinates);
            $this->updateBlockCoordinates($block, $coordinates);
//            $this->manager->persist($block);
        }

        // TODO content validation

//        TODO (in controller)
//        $this->manager->flush();
    }*/

    /**
     * Updates coordinates of the given block.
     *
     * @param BlockInterface $block
     * @param array          $coordinates
     */
    /*private function updateBlockCoordinates(BlockInterface $block, array $coordinates = [])
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
    }*/

    /**
     * Finds a block.
     *
     * @param array $data
     *
     * @return BlockInterface
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    /*private function findBlock(array $data = [])
    {
        if (!array_key_exists('id', $data) || 0 >= ($blockId = intval($data['id']))) {
            throw new \InvalidArgumentException('Block "id" is mandatory.');
        }
        $parameters = ['id' => $blockId];
        if (null !== $this->content) {
            $parameters['content'] = $this->content;
        }
        $block = $this->manager
            ->getRepository('EkynaCmsBundle:Block')
            ->findOneBy($parameters);
        if (null === $block) {
            throw new \RuntimeException('Block not found.');
        }

        return $block;
    }*/

    /**
     * Finds a block by name or creates if not exists.
     *
     * @param string $name the block name
     * @param string $type the block type
     * @param array  $data the block datas
     *
     * @return BlockInterface
     */
    /*public function findBlockByName($name, $type = null, array $data = [])
    {
        if (null === $type) {
            $type = $this->defaultBlockType;
        }

        $repository = $this->manager->getRepository('Ekyna\Bundle\CmsBundle\Entity\Block');
        if (null === $block = $repository->findOneBy(['name' => $name, 'content' => null])) {
            $block = $this->createDefaultBlock($type, $data);
            $block->setName($name);

//            TODO (in controller)
//            $this->manager->persist($block);
//            $this->manager->flush($block);
        } else {
            // TODO test block type ?
        }

        return $block;
    }*/
}
