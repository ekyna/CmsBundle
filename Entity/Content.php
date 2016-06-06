<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityTrait;
use Ekyna\Bundle\CoreBundle\Model\TimestampableTrait;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Content implements ContentInterface
{
    use TimestampableTrait;
    use TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var ArrayCollection|ContainerInterface[]
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
    public function addContainer(ContainerInterface $container)
    {
        $container->setContent($this);
        $this->containers->add($container);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeContainer(ContainerInterface $container)
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
