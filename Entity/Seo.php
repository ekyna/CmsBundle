<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Model as RM;
use Ekyna\Bundle\CmsBundle\Model as Cms;

/**
 * Class Seo
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Cms\SeoTranslationInterface translate($locale = null, $create = false)
 */
class Seo extends RM\AbstractTranslatable implements Cms\SeoInterface
{
    use RM\TaggedEntityTrait;

    /**
     * @var integer
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
     * @var boolean
     */
    protected $follow;

    /**
     * @var boolean
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
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function setChangefreq($changefreq)
    {
        $this->changefreq = $changefreq;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * {@inheritDoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritDoc}
     */
    public function getFollow()
    {
        return $this->follow;
    }

    /**
     * {@inheritDoc}
     */
    public function setFollow($follow)
    {
        $this->follow = (bool)$follow;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function setIndex($index)
    {
        $this->index = (bool)$index;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonical()
    {
        return $this->canonical;
    }

    /**
     * {@inheritDoc}
     */
    public function setCanonical($canonical)
    {
        $this->canonical = $canonical;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public static function getChangefreqs()
    {
        return ['hourly', 'monthly', 'yearly'];
    }

    /**
     * Returns whether the exhibitor should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable()
    {
        return $this->getIndex();
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.seo';
    }
}
