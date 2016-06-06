<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;

/**
 * Class ContainerManager
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerManager
{
    /**
     * Creates a new container.
     *
     * @param array                 $data
     * @param ContentInterface|null $content
     *
     * @return ContainerInterface
     */
    public function create(array $data = [], ContentInterface $content = null)
    {
        // Default data

        // New instance

        // Add container to content if available

        // Return
    }

    /**
     * Moves up the container.
     *
     * @param ContainerInterface $container
     */
    public function moveUp(ContainerInterface $container)
    {
        // Check if the container has a content

        // Check if the container is not the first one

        // Move up this container and move down the previous one
    }

    /**
     * Moves down the container.
     *
     * @param ContainerInterface $container
     */
    public function moveDown(ContainerInterface $container)
    {
        // Check if the container has a content

        // Check if it is not the last one

        // Move down this container and move up the next one
    }

    /**
     * Updates the container layout (blocks grid).
     *
     * @param ContainerInterface $container
     * @param array              $layout
     */
    public function updateLayout(ContainerInterface $container, array $layout = [])
    {
        // Validate layout

        // For each container blocks
            // Set row, column and size

        // Reorder blocks
    }

    /**
     * Updates the container data.
     *
     * @param ContainerInterface $container
     * @param array              $data
     */
    public function updateData(ContainerInterface $container, array $data = [])
    {
        // Validate data

        // Set data
    }

    /**
     * Deletes the container.
     *
     * @param ContainerInterface $container
     */
    public function delete(ContainerInterface $container)
    {
        // Check if the container is not the only content container (a content must have at least one container).

        // Update content layout
    }
}
