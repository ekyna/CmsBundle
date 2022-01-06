<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Copier\CopyInterface;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface ContainerInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ContainerInterface
    extends DataInterface,
            LayoutInterface,
            CopyInterface,
            RM\SortableInterface,
            RM\TimestampableInterface,
            RM\TaggedEntityInterface
{
    /**
     * Sets the content.
     *
     * @param ContentInterface|null $content
     *
     * @return ContainerInterface|$this
     */
    public function setContent(ContentInterface $content = null): ContainerInterface;

    /**
     * Returns the content.
     *
     * @return ContentInterface|null
     */
    public function getContent(): ?ContentInterface;

    /**
     * Sets the copied container.
     *
     * @param ContainerInterface|null $copy
     *
     * @return ContainerInterface|$this
     */
    public function setCopy(ContainerInterface $copy = null): ContainerInterface;

    /**
     * Returns the copied container.
     *
     * @return ContainerInterface|null
     */
    public function getCopy(): ?ContainerInterface;

    /**
     * Sets the name.
     *
     * @param string|null $name
     *
     * @return ContainerInterface|$this
     */
    public function setName(string $name = null): ContainerInterface;

    /**
     * Returns the name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Sets the title.
     *
     * @param string|null $title
     *
     * @return ContainerInterface|$this
     */
    public function setTitle(string $title = null): ContainerInterface;

    /**
     * Returns the title.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Sets the type.
     *
     * @param string $type
     *
     * @return ContainerInterface|$this
     */
    public function setType(string $type): ContainerInterface;

    /**
     * Returns the type.
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Sets the rows.
     *
     * @param ArrayCollection|RowInterface[] $rows
     *
     * @return ContainerInterface|$this
     */
    public function setRows(ArrayCollection $rows): ContainerInterface;

    /**
     * Adds the row.
     *
     * @param RowInterface $row
     *
     * @return ContainerInterface|$this
     */
    public function addRow(RowInterface $row): ContainerInterface;

    /**
     * Removes the row.
     *
     * @param RowInterface $row
     *
     * @return ContainerInterface|$this
     */
    public function removeRow(RowInterface $row): ContainerInterface;

    /**
     * Returns the rows.
     *
     * @return Collection|RowInterface[]
     */
    public function getRows(): Collection;

    /**
     * Returns whether or not the container is the first of the content.
     *
     * @return bool
     */
    public function isFirst(): bool;

    /**
     * Returns whether or not the container is the last of the content.
     *
     * @return bool
     */
    public function isLast(): bool;

    /**
     * Returns whether or not the container is the only content's child.
     *
     * @return bool
     */
    public function isAlone(): bool;

    /**
     * Returns whether or not the container is named.
     *
     * @return bool
     */
    public function isNamed(): bool;

    /**
     * Returns whether or not the container is titled.
     *
     * @return bool
     */
    public function isTitled(): bool;
}
