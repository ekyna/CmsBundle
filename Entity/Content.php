<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Content implements Cms\ContentInterface
{
    use Core\TimestampableTrait,
        Core\TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var ArrayCollection|Cms\ContainerInterface[]
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainers(ArrayCollection $containers)
    {
        foreach ($containers as $container) {
            $this->addContainer($container);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addContainer(Cms\ContainerInterface $container)
    {
        $container->setContent($this);
        $this->containers->add($container);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeContainer(Cms\ContainerInterface $container)
    {
        $container->setContent(null);
        $this->containers->removeElement($container);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * {@inheritdoc}
     */
    public function sortContainers()
    {
        $iterator = $this->containers->getIterator();
        $iterator->uasort(function (Cms\ContainerInterface $a, Cms\ContainerInterface $b) {
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });
        $this->containers = new ArrayCollection(iterator_to_array($iterator));

        return $this;
    }

    /**
     * {@inheritdoc}
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents()
    {
        $contents = [];

        /* TODO foreach ($this->blocks as $block) {
            if ($block->isIndexable()) {
                foreach ($block->getIndexableContents() as $locale => $content) {
                    if (!array_key_exists($locale, $contents)) {
                        $contents[$locale] = array('content' => '');
                    }
                    $contents[$locale]['content'] .= $content;
                }
            }
        }*/

        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.content';
    }
}
