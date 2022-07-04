<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model;

/**
 * Class RowManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowManager extends AbstractManager
{
    /**
     * Creates a new row.
     *
     * @throws InvalidOperationException
     */
    public function create($containerOrName, array $data = []): Model\RowInterface
    {
        // Check if parent or name is defined
        if (is_string($containerOrName) && empty($containerOrName)) {
            throw new InvalidOperationException('Excepted instance of ContainerInterface or non-empty string.');
        }

        // New instance
        $row = $this->editor->getRepository()->createRow();

        // Create default block
        $this->editor->getBlockManager()->create($row);

        // Add to row if available
        if ($containerOrName instanceof Model\ContainerInterface) {
            $count = $containerOrName->getRows()->count();
            $row->setPosition($count);
            $containerOrName->addRow($row);
        } else {
            $row->setName($containerOrName);
        }

        return $row;
    }

    /**
     * Deletes the row.
     *
     * @return Model\RowInterface The removed row.
     * @throws InvalidOperationException
     */
    public function delete(Model\RowInterface $row, bool $force = false): Model\RowInterface
    {
        $container = $row->getContainer();

        // Skip checks if forced
        if (!$force) {
            // Don't remove container's only row or named row
            if ($row->isAlone() || $row->isNamed()) {
                throw new InvalidOperationException(
                    "The row can't be removed because it is named or the parent container does not have enough children."
                );
            }

            // Don't remove standalone row
            if (null === $container) {
                throw new InvalidOperationException(
                    "The row does not belong to a container and therefore can't be removed."
                );
            }
        }

        $container->removeRow($row);

        $this->editor
            ->getContainerManager()
            ->fixRowsPositions($container);

        return $row;
    }

    /**
     * Moves the row up.
     *
     * @return Model\RowInterface the sibling row that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveUp(Model\RowInterface $row): Model\RowInterface
    {
        $sibling = $this->editor->getRepository()->findSiblingRow($row, false);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The row can't be moved up as no sibling row has been found."
            );
        }

        $row->setPosition($row->getPosition() - 1);
        $sibling->setPosition($sibling->getPosition() + 1);

        $this->sortChildrenByPosition($row->getContainer(), 'rows');

        return $sibling;
    }

    /**
     * Moves the row down.
     *
     * @return Model\RowInterface the sibling row that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveDown(Model\RowInterface $row): Model\RowInterface
    {
        $sibling = $this->editor->getRepository()->findSiblingRow($row, true);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The row can't be moved down as no sibling row has been found."
            );
        }

        $row->setPosition($row->getPosition() + 1);
        $sibling->setPosition($sibling->getPosition() - 1);

        $this->sortChildrenByPosition($row->getContainer(), 'rows');

        return $sibling;
    }

    /**
     * Fixes the blocks positions.
     */
    public function fixBlocksPositions(Model\RowInterface $row): RowManager
    {
        $this->sortChildrenByPosition($row, 'blocks');

        /** @var Model\BlockInterface[] $blocks */
        $blocks = $row->getBlocks();

        $position = 0;
        foreach ($blocks as $block) {
            $block->setPosition($position);
            $position++;
        }

        return $this;
    }
}
