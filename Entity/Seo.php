<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Seo
 * @package      Ekyna\Bundle\CmsBundle\Entity
 * @author       Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Cms\SeoTranslationInterface translate($locale = null, $create = false)
 * @method Collection|Cms\SeoTranslationInterface[] getTranslations()
 */
class Seo extends RM\AbstractTranslatable implements Cms\SeoInterface
{
    use RM\TaggedEntityTrait;

    protected ?int    $id         = null;
    protected string  $changefreq = Cms\ChangeFrequencies::MONTHLY;
    protected string  $priority   = '0.5';
    protected bool    $follow     = true;
    protected bool    $index      = true;
    protected ?string $canonical  = null;


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
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title = null): Cms\SeoInterface
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description = null): Cms\SeoInterface
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->translate()->getDescription();
    }

    /**
     * @inheritDoc
     */
    public function setChangefreq(string $changefreq): Cms\SeoInterface
    {
        $this->changefreq = $changefreq;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChangefreq(): string
    {
        return $this->changefreq;
    }

    /**
     * @inheritDoc
     */
    public function setPriority(string $priority): Cms\SeoInterface
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function setFollow(bool $follow): Cms\SeoInterface
    {
        $this->follow = $follow;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFollow(): bool
    {
        return $this->follow;
    }

    /**
     * @inheritDoc
     */
    public function setIndex(bool $index): Cms\SeoInterface
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): bool
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function setCanonical(string $canonical = null): Cms\SeoInterface
    {
        $this->canonical = $canonical;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCanonical(): ?string
    {
        return $this->canonical;
    }

    /**
     * Returns whether the seo should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable(): bool
    {
        return $this->getIndex();
    }

    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.seo';
    }
}
