<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Component\Resource\Model as RM;
use Ekyna\Bundle\CmsBundle\Model as Cms;

/**
 * Class Seo
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Cms\SeoTranslationInterface translate($locale = null, $create = false)
 * @method \Doctrine\Common\Collections\Collection|Cms\SeoTranslationInterface[] getTranslations()
 */
class Seo extends RM\AbstractTranslatable implements Cms\SeoInterface
{
    use RM\TaggedEntityTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $changefreq;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $follow;

    /**
     * @var bool
     */
    protected $index;

    /**
     * @var string
     */
    protected $canonical;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->changefreq = 'monthly';
        $this->priority = 0.5;
        $this->follow = true;
        $this->index = true;
    }

    /**
     * Clones the seo.
     */
    public function __clone()
    {
        parent::__clone();

        $this->id = null;
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTitle() ?: 'New Seo';
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * @inheritdoc
     */
    public function setChangefreq($changefreq)
    {
        $this->changefreq = $changefreq;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function getFollow()
    {
        return $this->follow;
    }

    /**
     * @inheritdoc
     */
    public function setFollow($follow)
    {
        $this->follow = (bool)$follow;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function setIndex($index)
    {
        $this->index = (bool)$index;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCanonical()
    {
        return $this->canonical;
    }

    /**
     * @inheritdoc
     */
    public function setCanonical($canonical)
    {
        $this->canonical = $canonical;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getChangefreqs()
    {
        return ['hourly', 'monthly', 'yearly'];
    }

    /**
     * Returns whether the seo should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable()
    {
        return $this->getIndex();
    }

    /**
     * @inheritdoc
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.seo';
    }
}
