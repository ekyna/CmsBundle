<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlockManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockManager extends AbstractManager
{
    /**
     * @var string
     */
    private $defaultType;


    /**
     * Constructor.
     *
     * @param string $defaultType
     */
    public function __construct($defaultType)
    {
        $this->defaultType = $defaultType;
    }

    /**
     * Creates a block.
     *
     * @param Model\RowInterface|string $rowOrName
     * @param string|null               $type
     * @param array                     $data
     *
     * @throws InvalidOperationException
     *
     * @return Model\BlockInterface
     */
    public function create($rowOrName, $type = null, array $data = [])
    {
        // Check if row or name is defined
        if (!(
            $rowOrName instanceof Model\RowInterface ||
            (is_string($rowOrName) && 0 < strlen($rowOrName))
        )
        ) {
            throw new InvalidOperationException("Excepted instance of RowInterface or string.");
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
            $block
                ->setPosition($count);

            $rowOrName->addBlock($block);
        } else {
            $block->setName($rowOrName);
        }

        return $block;
    }

    /**
     * Updates the block.
     *
     * @param Model\BlockInterface $block
     * @param Request              $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @throws InvalidOperationException
     */
    public function update(Model\BlockInterface $block, Request $request)
    {
        // Plugin update
        return $this->editor
            ->getBlockPlugin($block->getType())
            ->update($block, $request);
    }

    /**
     * Changes the block type.
     *
     * @param Model\BlockInterface $block The block
     * @param string               $type  The block new type
     * @param array                $data  The block new data
     *
     * @throws InvalidOperationException
     */
    public function changeType(Model\BlockInterface $block, $type, array $data = [])
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
     * @param Model\BlockInterface $block
     *
     * @return Model\BlockInterface The removed block.
     * @throws InvalidOperationException
     */
    public function delete(Model\BlockInterface $block)
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
     * @param Model\BlockInterface $block
     *
     * @return Model\RowInterface The sibling row where the block has been moved into.
     * @throws InvalidOperationException
     */
    public function moveUp(Model\BlockInterface $block)
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
     * @param Model\BlockInterface $block
     *
     * @return Model\RowInterface The sibling row where the block has been moved into.
     * @throws InvalidOperationException
     */
    public function moveDown(Model\BlockInterface $block)
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
     * @param Model\BlockInterface $block
     *
     * @return Model\BlockInterface The sibling block that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveLeft(Model\BlockInterface $block)
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
     * @param Model\BlockInterface $block
     *
     * @return Model\BlockInterface The sibling block that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveRight(Model\BlockInterface $block)
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
