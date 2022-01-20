<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Component\Resource\Copier\CopierInterface;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Container
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Container implements EM\ContainerInterface
{
    use EM\DataTrait;
    use EM\LayoutTrait;
    use RM\SortableTrait;
    use RM\TimestampableTrait;
    use RM\TaggedEntityTrait {
        getEntityTag as traitGetEntityTag;
    }

    protected ?int                   $id      = null;
    protected ?EM\ContentInterface   $content = null;
    protected ?EM\ContainerInterface $copy    = null;
    protected ?string                $name    = null;
    protected ?string                $title   = null;
    protected ?string                $type    = null;
    protected Collection             $rows;

    public function __construct()
    {
        $this->position = 0;
        $this->rows = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $this->content = null;
    }

    public function onCopy(CopierInterface $copier): void
    {
        $this->copy = null;
        $copier->copyCollection($this, 'rows', true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setContent(EM\ContentInterface $content = null): EM\ContainerInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): ?EM\ContentInterface
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function setCopy(EM\ContainerInterface $copy = null): EM\ContainerInterface
    {
        $this->copy = $copy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCopy(): ?EM\ContainerInterface
    {
        return $this->copy;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name = null): EM\ContainerInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title = null): EM\ContainerInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): EM\ContainerInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setRows(ArrayCollection $rows): EM\ContainerInterface
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addRow(EM\RowInterface $row): EM\ContainerInterface
    {
        $row->setContainer($this);
        $this->rows->add($row);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeRow(EM\RowInterface $row): EM\ContainerInterface
    {
        $row->setContainer();
        $this->rows->removeElement($row);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRows(): Collection
    {
        return $this->rows;
    }

    /**
     * @inheritDoc
     */
    public function isFirst(): bool
    {
        return 0 == $this->position;
    }

    /**
     * @inheritDoc
     */
    public function isLast(): bool
    {
        if ($this->content && ($this->content->getContainers()->count() - 1 > $this->position)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isAlone(): bool
    {
        if (null === $this->content) {
            return true;
        }

        return 1 >= $this->content->getContainers()->count();
    }

    /**
     * @inheritDoc
     */
    public function isNamed(): bool
    {
        return !empty($this->name);
    }

    /**
     * @inheritDoc
     */
    public function isTitled(): bool
    {
        return !empty($this->title);
    }

    /**
     * @inheritDoc
     */
    public function getEntityTag(): string
    {
        if (empty($this->name) && null !== $this->content) {
            return $this->content->getEntityTag();
        }

        return $this->traitGetEntityTag();
    }

    /**
     * Returns the entity tag.
     *
     * @return string
     */
    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.container';
    }
}
