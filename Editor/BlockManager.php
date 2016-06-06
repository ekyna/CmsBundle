<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContainerInterface;

/**
 * Class BlockManager
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockManager
{
    /**
     * @param string|null             $type
     * @param array                   $data
     * @param ContainerInterface|null $container
     * @param string|null             $name
     *
     * @return BlockInterface
     */
    public function create(
        string $type = null,
        array $data = [],
        ContainerInterface $container = null,
        string $name = null
    ): BlockInterface
    {
        // Check if container or name is defined

        // Default type if null

        // Check if type is valid

        // Default data

        // New instance

        // Add to container if available
        // else set name

        // return
    }

    /**
     * Moves the block to the left.
     *
     * @param BlockInterface $block
     */
    public function moveLeft(BlockInterface $block)
    {

    }

    public function updateType(BlockInterface $block, string $type, array $data = [])
    {
        // Validate new type

        // Set type

        // Set default data (through plugin)
    }

    public function updateData(BlockInterface $block, array $data = [])
    {
        // Validate data

        // Set data (through plugin)
    }

    public function delete(BlockInterface $block)
    {
        // Check if the block is not the only container block (a container must have at least one block).

        // Update container layout
    }
}
