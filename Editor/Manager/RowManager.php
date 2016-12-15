<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Model;

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
        )) {
            throw new InvalidOperationException("Excepted instance of ContainerInterface or string.");
        }

        $editor = $this->getEditor();

        // New instance
        $row = $this->getEditor()->getRepository()->createRow();

        // Create default block
        $editor->getBlockManager()->create($row);

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
        // Check if the row is not the only container row (a container must have at least one row).
        $container = $row->getContainer();
        if (null === $container) {
            throw new InvalidOperationException(
                "The row does not belong to a container and therefore can't be removed."
            );
        }

        $rows = $row->getContainer()->getRows();
        if (1 >= $rows->count()) {
            throw new InvalidOperationException(
                "The row can't be removed because the parent container does not have enough children."
            );
        }

        $rows->removeElement($row);

        $this
            ->getEditor()
            ->getContainerManager()
            ->fixRowPositions($container);

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
        $sibling = $this->findPreviousSibling($row);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The row can't be moved up as no previous sibling has been found."
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
        $sibling = $this->findNextSibling($row);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The row can't be moved down as no next sibling has been found."
            );
        }

        $row->setPosition($row->getPosition() + 1);
        $sibling->setPosition($sibling->getPosition() - 1);

        $this->sortChildrenByPosition($row->getContainer(), 'rows');

        return $sibling;
    }

    /**
     * Fix the blocks sizes.
     *
     * @param Model\RowInterface $row
     *
     * @return RowManager
     */
    public function fixBlockSizes(Model\RowInterface $row)
    {
        $blocks = $row->getBlocks();

        $total = 0;
        foreach ($blocks as $block) {
            $total += $block->getSize();
        }

        $diff = 12 - $total;
        if (0 == $diff) {
            return $this;
        }

        if (0 < $diff) { // too small
            $avg = ceil(12 / $blocks->count());
            $mod = 1;
        } else { // too large
            $avg = floor(12 / $blocks->count());
            $mod = -1;
        }

        while (0 != $diff) {
            foreach ($blocks as $block) {
                if ($avg != $block->getSize()) {
                    $block->setSize($block->getSize() + $mod);
                    $diff -= $mod;

                    if (0 == $diff) {
                        break 2;
                    }
                }
            }
            reset($blocks);
        }

        return $this;
    }

    /**
     * Fix the block positions.
     *
     * @param Model\RowInterface $row
     *
     * @return RowManager
     */
    public function fixBlockPositions(Model\RowInterface $row)
    {
        $this->sortChildrenByPosition($row, 'blocks');

        $blocks = $row->getBlocks();

        $position = 0;
        foreach ($blocks as $block) {
            $block->setPosition($position);
            $position++;
        }

        return $this;
    }

    /**
     * The row's previous sibling.
     *
     * @param Model\RowInterface $row
     *
     * @return Model\RowInterface|null
     * @throws InvalidOperationException
     */
    private function findPreviousSibling(Model\RowInterface $row)
    {
        if (null === $container = $row->getContainer()) {
            throw new InvalidOperationException('The row does not have a parent container.');
        }

        // Return null if this is the first row
        if (0 == $row->getPosition()) {
            return null;
        }

        $rows = $container->getRows();

        $sibling = $rows->filter(function (Model\RowInterface $b) use ($row) {
            return $b->getPosition() == $row->getPosition() -1;
        })->first();

        return $sibling ? $sibling : null;
    }

    /**
     * Finds the row's next sibling.
     *
     * @param Model\RowInterface $row
     *
     * @return Model\RowInterface|null
     * @throws InvalidOperationException
     */
    private function findNextSibling(Model\RowInterface $row)
    {
        if (null === $container = $row->getContainer()) {
            throw new InvalidOperationException('The row does not have a parent container.');
        }

        $rows = $container->getRows();

        // Return null if this is the last row
        if ($rows->count() -1 == $row->getPosition()) {
            return null;
        }

        $sibling = $rows->filter(function (Model\RowInterface $b) use ($row) {
            return $b->getPosition() == $row->getPosition() + 1;
        })->first();

        return $sibling ? $sibling : null;
    }
}
