<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * AbstractBlock.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractBlock implements BlockInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\Content
     */
    protected $content;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $row = 1;

    /**
     * @var integer
     */
    protected $column = 1;

    /**
     * @var integer
     */
    protected $size = 12;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(ContentInterface $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getInitDatas()
    {
        return array(
        	'id'     => $this->id,
            'type'   => $this->getType(),
        	'row'    => intval($this->row),
        	'column' => intval($this->column),
        	'size'   => intval($this->size)            
        );
    }

    /**
     * Validates the block (content or key have to be set, but not both).
     *
     * @param ExecutionContextInterface $context
     * @return bool
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (($this->content === null && 0 < strlen($this->key)) || (null !== $this->content && null === $this->key)) {
            $context->addViolation('Content or key must be defined, but not both.');
        }
    }
}
