<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Container
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Container implements Cms\ContainerInterface
{
    use Core\SortableTrait,
        Core\TimestampableTrait,
        Core\TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Cms\ContentInterface
     */
    protected $content;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ArrayCollection|Cms\BlockInterface[]
     */
    protected $blocks;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
        $this->blocks = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setContent(Cms\ContentInterface $content = null)
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
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setBlocks(ArrayCollection $blocks)
    {
        foreach ($blocks as $block) {
            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addBlock(Cms\BlockInterface $block)
    {
        $block->setContainer($this);
        $this->blocks->add($block);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeBlock(Cms\BlockInterface $block)
    {
        $block->setContainer(null);
        $this->blocks->removeElement($block);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        return $this->blocks;
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
     * Returns the entity tag.
     *
     * @return string
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.container';
    }
}
