<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlockManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockManager extends AbstractManager
{
    public function __construct(private readonly string $defaultType)
    {
    }

    /**
     * Creates a block.
     *
     * @throws InvalidOperationException
     */
    public function create(
        Model\RowInterface|string $rowOrName,
        string                    $type = null,
        array                     $data = []
    ): Model\BlockInterface {
        // Check if row or name is defined
        if (is_string($rowOrName) && empty($rowOrName)) {
            throw new InvalidOperationException('Excepted instance of RowInterface or non-empty string.');
        }

        // Default type if null
        if (null === $type) {
            $type = $this->defaultType;
        }

        // New instance
        $block = $this->editor->getRepository()->createBlock();
        $block->setType($type);

        // Layout creation
//        $this->editor->getLayoutAdapter()->createBlock($block, $data);

        // Plugin creation
        $this->editor->getBlockPlugin($type)->create($block, $data);

        // Add to row if available
        if ($rowOrName instanceof Model\RowInterface) {
            $count = $rowOrName->getBlocks()->count();

            $block->setPosition($count);

            $rowOrName->addBlock($block);
        } else {
            $block->setName($rowOrName);
        }

        return $block;
    }

    /**
     * Updates the block.
     *
     * @throws EditorExceptionInterface
     */
    public function update(Model\BlockInterface $block, Request $request): ?Response
    {
        // Plugin update
        return $this->editor
            ->getBlockPlugin($block->getType())
            ->update($block, $request);
    }

    /**
     * Changes the block type.
     *
     * @throws InvalidOperationException
     */
    public function changeType(Model\BlockInterface $block, string $type, array $data = [])
    {
        if ($type === $block->getType()) {
            return;
        }

        if ($block->isNamed()) {
            throw new InvalidOperationException(
                "The type of this block can't be changed."
            );
        }

        // Plugin removal
        $this->editor
            ->getBlockPlugin($block->getType())
            ->remove($block);

        // Sets the new type
        $block->setType($type);

        // Plugin creation
        $this->editor
            ->getBlockPlugin($block->getType())
            ->create($block, $data);
    }

    /**
     * Deletes the block.
     *
     * @return Model\BlockInterface The removed block.
     * @throws InvalidOperationException
     */
    public function delete(Model\BlockInterface $block): Model\BlockInterface
    {
        // Ensure not named / alone
        if ($block->isAlone() || $block->isNamed()) {
            throw new InvalidOperationException(
                "The block can't be removed because it is named or the parent row does not have enough children."
            );
        }

        if (null === $row = $block->getRow()) {
            throw new InvalidOperationException(
                "This block does not belong to a row and therefore can't be removed."
            );
        }

        // Plugin remove
        $this->editor
            ->getBlockPlugin($block->getType())
            ->remove($block);

        // Remove from row
        $row->removeBlock($block);

        // Fix row's blocks positions
        $this->editor
            ->getRowManager()
            ->fixBlocksPositions($row);

        return $block;
    }

    /**
     * Moves the block up.
     *
     * @return Model\RowInterface The sibling row where the block has been moved into.
     * @throws InvalidOperationException
     */
    public function moveUp(Model\BlockInterface $block): Model\RowInterface
    {
        $row = $block->getRow();
        if (null === $row || $row->isFirst()) {
            throw new InvalidOperationException(
                "This block can't be moved to the top."
            );
        }

        $sibling = $this->editor->getRepository()->findSiblingRow($row, false);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The block can't be moved to the top as no sibling row has been found."
            );
        }

        $row->removeBlock($block);
        $sibling->addBlock($block);

        $this->editor
            ->getRowManager()
            ->fixBlocksPositions($row)
            ->fixBlocksPositions($sibling);

        return $sibling;
    }

    /**
     * Moves the block down.
     *
     * @return Model\RowInterface The sibling row where the block has been moved into.
     * @throws InvalidOperationException
     */
    public function moveDown(Model\BlockInterface $block): Model\RowInterface
    {
        $row = $block->getRow();
        if (null === $row || $row->isLast()) {
            throw new InvalidOperationException(
                "This block can't be moved to the top."
            );
        }

        $sibling = $this->editor->getRepository()->findSiblingRow($row, true);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The block can't be moved to the bottom as no sibling row has been found."
            );
        }

        $row->removeBlock($block);
        $sibling->addBlock($block);

        $this->editor
            ->getRowManager()
            ->fixBlocksPositions($row)
            ->fixBlocksPositions($sibling);

        return $sibling;
    }

    /**
     * Moves the block to the left.
     *
     * @return Model\BlockInterface The sibling block that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveLeft(Model\BlockInterface $block): Model\BlockInterface
    {
        $sibling = $this->editor->getRepository()->findSiblingBlock($block, false);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The block can't be moved to the right as no sibling block has been found."
            );
        }

        $block->setPosition($block->getPosition() - 1);
        $sibling->setPosition($sibling->getPosition() + 1);

        $this->sortChildrenByPosition($block->getRow(), 'blocks');

        return $sibling;
    }

    /**
     * Moves the block to the right.
     *
     * @return Model\BlockInterface The sibling block that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveRight(Model\BlockInterface $block): Model\BlockInterface
    {
        $sibling = $this->editor->getRepository()->findSiblingBlock($block, true);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The block can't be moved to the right as no sibling block has been found."
            );
        }

        $block->setPosition($block->getPosition() + 1);
        $sibling->setPosition($sibling->getPosition() - 1);

        $this->sortChildrenByPosition($block->getRow(), 'blocks');

        return $sibling;
    }
}
