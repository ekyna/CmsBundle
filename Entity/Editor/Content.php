<?php

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Content implements EM\ContentInterface
{
    use RM\TimestampableTrait,
        RM\TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ArrayCollection|EM\ContainerInterface[]
     */
    protected $containers;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->containers = new ArrayCollection();
    }

    /**
     * Clones the content.
     */
    public function __clone()
    {
        if ($this->id) {
            $this->id = null;

            $containers = $this->containers->toArray();
            $this->containers = new ArrayCollection();
            foreach ($containers as $container) {
                $this->addContainer(clone $container);
            }
        }
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
    public function setContainers(ArrayCollection $containers)
    {
        foreach ($containers as $container) {
            $this->addContainer($container);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addContainer(EM\ContainerInterface $container)
    {
        $container->setContent($this);
        $this->containers->add($container);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeContainer(EM\ContainerInterface $container)
    {
        $container->setContent(null);
        $this->containers->removeElement($container);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContainers()
    {
        return $this->containers;
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
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.content';
    }
}
