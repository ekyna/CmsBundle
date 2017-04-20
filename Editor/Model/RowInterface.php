<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface RowInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface RowInterface
    extends LayoutInterface,
            RM\SortableInterface,
            RM\TimestampableInterface,
            RM\TaggedEntityInterface
{
    /**
     * Set container
     *
     * @param ContainerInterface|null $container
     *
     * @return RowInterface|$this
     */
    public function setContainer(ContainerInterface $container = null): RowInterface;

    /**
     * Get container
     *
     * @return ContainerInterface|null
     */
    public function getContainer(): ?ContainerInterface;

    /**
     * Sets the name
     *
     * @param string|null $name
     *
     * @return RowInterface|$this
     */
    public function setName(string $name = null): RowInterface;

    /**
     * Returns the name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set blocks
     *
     * @param Collection|BlockInterface[] $blocks
     *
     * @return RowInterface|$this
     */
    public function setBlocks(Collection $blocks): RowInterface;

    /**
     * Add block
     *
     * @param BlockInterface $block
     *
     * @return RowInterface|$this
     */
    public function addBlock(BlockInterface $block): RowInterface;

    /**
     * Remove block
     *
     * @param BlockInterface $block
     *
     * @return RowInterface|$this
     */
    public function removeBlock(BlockInterface $block): RowInterface;

    /**
     * Get blocks
     *
     * @return Collection|BlockInterface[]
     */
    public function getBlocks(): Collection;

    /**
     * Returns whether or not the row is the first of the container.
     *
     * @return bool
     */
    public function isFirst(): bool;

    /**
     * Returns whether or not the row is the last of the container.
     *
     * @return bool
     */
    public function isLast(): bool;

    /**
     * Returns whether or not the row is the only container's child.
     *
     * @return bool
     */
    public function isAlone(): bool;

    /**
     * Returns whether or not the row is named.
     *
     * @return bool
     */
    public function isNamed(): bool;
}
