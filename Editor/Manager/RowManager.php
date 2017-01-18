<?php

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
     * @param Model\ContainerInterface|string $containerOrName
     * @param array                           $data
     *
     * @return Model\RowInterface
     * @throws InvalidOperationException
     */
    public function create($containerOrName, array $data = [])
    {
        // Check if parent or name is defined
        if (!(
            $containerOrName instanceof Model\ContainerInterface ||
            (is_string($containerOrName) && 0 < strlen($containerOrName))
        )
        ) {
            throw new InvalidOperationException("Excepted instance of ContainerInterface or string.");
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
     * @param Model\RowInterface $row
     *
     * @return Model\RowInterface The removed row.
     * @throws InvalidOperationException
     */
    public function delete(Model\RowInterface $row)
    {
        // Ensure not named / alone
        if ($row->isAlone() || $row->isNamed()) {
            throw new InvalidOperationException(
                "The row can't be removed because it is named or the parent container does not have enough children."
            );
        }

        // Check if the row is not the only container row (a container must have at least one row).
        if (null === $container = $row->getContainer()) {
            throw new InvalidOperationException(
                "The row does not belong to a container and therefore can't be removed."
            );
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
     * @param Model\RowInterface $row
     *
     * @return Model\RowInterface the sibling row that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveUp(Model\RowInterface $row)
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
     * @param Model\RowInterface $row
     *
     * @return Model\RowInterface the sibling row that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveDown(Model\RowInterface $row)
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
     * Fix the blocks positions.
     *
     * @param Model\RowInterface $row
     *
     * @return RowManager
     */
    public function fixBlocksPositions(Model\RowInterface $row)
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
