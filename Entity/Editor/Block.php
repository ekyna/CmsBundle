<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Component\Resource\Copier\CopierInterface;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Block
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method EM\BlockTranslationInterface translate(string $locale = null, bool $create = false)
 */
class Block extends RM\AbstractTranslatable implements EM\BlockInterface
{
    use EM\DataTrait;
    use EM\LayoutTrait;
    use RM\SortableTrait;
    use RM\TimestampableTrait;

    use RM\TaggedEntityTrait {
        getEntityTag as traitGetEntityTag;
    }

    protected ?EM\RowInterface $row  = null;
    protected ?string          $name = null;
    protected ?string          $type = null;

    public function __clone()
    {
        parent::__clone();

        $this->row = null;
    }

    public function onCopy(CopierInterface $copier): void
    {
        $this->name = null;
    }

    /**
     * @inheritDoc
     */
    public function setRow(EM\RowInterface $row = null): EM\BlockInterface
    {
        $this->row = $row;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRow(): ?EM\RowInterface
    {
        return $this->row;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name = null): EM\BlockInterface
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
    public function setType(string $type): EM\BlockInterface
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
    public function isFirst(): bool
    {
        return 0 == $this->position;
    }

    /**
     * @inheritDoc
     */
    public function isLast(): bool
    {
        if ($this->row && ($this->row->getBlocks()->count() - 1 > $this->position)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isAlone(): bool
    {
        if (null === $this->row) {
            return true;
        }

        return 1 >= $this->row->getBlocks()->count();
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
        if (empty($this->name) && null !== $this->row) {
            return $this->row->getEntityTag();
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
        return 'ekyna_cms.block';
    }
}
