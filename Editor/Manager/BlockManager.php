<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\PluginRegistry;
use Ekyna\Bundle\CmsBundle\Entity;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class BlockManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockManager
{
    /**
     * @var PluginRegistry
     */
    private $pluginRegistry;


    /**
     * Constructor.
     *
     * @param PluginRegistry $pluginRegistry
     */
    public function __construct(PluginRegistry $pluginRegistry)
    {
        $this->pluginRegistry = $pluginRegistry;
    }

    /**
     * Creates a block.
     *
     * @param Model\RowInterface|string $rowOrName
     * @param string|null               $type
     * @param array                     $data
     *
     * @throws \Exception
     *
     * @return Model\BlockInterface
     */
    public function create($rowOrName, $type = null, array $data = [])
    {
        // Check if row or name is defined
        if (!$rowOrName instanceof Model\RowInterface || (is_string($rowOrName) && 0 == strlen($rowOrName))) {
            throw new \Exception('Excepted instance of RowInterface or string.');
        }

        // Default type if null
        if (null === $type) {
            $type = 'ekyna_cms_tinymce'; // TODO parameter
        }

        // Check if type is valid
        $plugin = $this->pluginRegistry->get($type);

        // New instance
        $block = new Entity\Block();
        $block->setType($type);

        // TODO Default data
        $plugin->create($block, $data);

        // Add to row if available
        if ($rowOrName instanceof Model\RowInterface) {
            $count = $rowOrName->getBlocks()->count();
            $block
                ->setPosition($count)
                ->setSize(floor(12 / ($count + 1)));
            $rowOrName->addBlock($block);
        } else {
            $block->setName($rowOrName);
        }

        // return
        return $block;
    }

    /**
     * Moves the block to the left.
     *
     * @param Model\BlockInterface $block
     */
    public function moveLeft(Model\BlockInterface $block)
    {

    }

    public function changeType(Model\BlockInterface $block, string $type, array $data = [])
    {
        // Validate new type

        // Set type

        // Set default data (through plugin)
    }

    public function updateData(Model\BlockInterface $block, array $data = [])
    {
        // Validate data

        // Set data (through plugin)
    }

    public function delete(Model\BlockInterface $block)
    {
        // Check if the block is not the only container block (a container must have at least one block).

        // Update container layout
    }
}
