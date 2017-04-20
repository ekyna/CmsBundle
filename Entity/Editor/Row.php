<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Row
 * @package      Ekyna\Bundle\CmsBundle\Entity
 * @author       Etienne Dauvergne <contact@ekyna.com>
 */
class Row implements EM\RowInterface
{
    use EM\LayoutTrait;
    use RM\SortableTrait;
    use RM\TimestampableTrait;

    use RM\TaggedEntityTrait {
        getEntityTag as traitGetEntityTag;
    }


    protected ?int                   $id        = null;
    protected ?EM\ContainerInterface $container = null;
    protected ?string                $name      = null;
    protected Collection             $blocks;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->position = 0;
        $this->blocks = new ArrayCollection();
    }

    /**
     * Clones the row.
     */
    public function __clone()
    {
        $this->id = null;
        $this->container = null;

        $blocks = $this->blocks->toArray();
        $this->blocks = new ArrayCollection();
        foreach ($blocks as $block) {
            $this->addBlock(clone $block);
        }
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(EM\ContainerInterface $container = null): EM\RowInterface
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?EM\ContainerInterface
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name = null): EM\RowInterface
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
    public function setBlocks(Collection $blocks): EM\RowInterface
    {
        foreach ($blocks as $block) {
            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBlock(EM\BlockInterface $block): EM\RowInterface
    {
        $block->setRow($this);
        $this->blocks->add($block);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeBlock(EM\BlockInterface $block): EM\RowInterface
    {
        $block->setRow();
        $this->blocks->removeElement($block);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
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
        if ($this->container && ($this->container->getRows()->count() - 1 > $this->position)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isAlone(): bool
    {
        if (null === $this->container) {
            return true;
        }

        return 1 >= $this->container->getRows()->count();
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
    public function getEntityTag(): string
    {
        if (empty($this->name) && null !== $this->container) {
            return $this->container->getEntityTag();
        }

        return $this->traitGetEntityTag();
    }

    /**
     * @inheritDoc
     */
    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.row';
    }
}
