<?php

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Container
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Container implements EM\ContainerInterface
{
    use EM\DataTrait,
        EM\LayoutTrait,
        RM\SortableTrait,
        RM\TimestampableTrait;

    use RM\TaggedEntityTrait {
        getEntityTag as traitGetEntityTag;
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var EM\ContentInterface
     */
    protected $content;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ArrayCollection|EM\RowInterface[]
     */
    protected $rows;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
        $this->rows = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setContent(EM\ContentInterface $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setRows(ArrayCollection $rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addRow(EM\RowInterface $row)
    {
        $row->setContainer($this);
        $this->rows->add($row);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeRow(EM\RowInterface $row)
    {
        $row->setContainer(null);
        $this->rows->removeElement($row);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @inheritdoc
     */
    public function isFirst()
    {
        return 0 == $this->position;
    }

    /**
     * @inheritdoc
     */
    public function isLast()
    {
        if (null !== $this->content && ($this->content->getContainers()->count() - 1 > $this->position)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function isAlone()
    {
        if (null === $this->content) {
            return true;
        }

        return 1 >= $this->content->getContainers()->count();
    }

    /**
     * @inheritdoc
     */
    public function isNamed()
    {
        return 0 < strlen($this->name);
    }

    /**
     * @inheritdoc
     */
    public function getEntityTag()
    {
        if (0 == strlen($this->name) && null !== $this->content) {
            return $this->content->getEntityTag();
        }

        return $this->traitGetEntityTag();
    }

    /**
     * Returns the entity tag.
     *
     * @return string
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.container';
    }
}
