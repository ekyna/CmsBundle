<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model\TreeTrait;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Page
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Cms\PageTranslationInterface translate($locale = null, $create = false)
 * @method Cms\PageTranslationInterface[] getTranslations()
 */
class Page extends RM\AbstractTranslatable implements Cms\PageInterface
{
    use Cms\ContentSubjectTrait;
    use Cms\SeoSubjectTrait;
    use RM\TimestampableTrait;
    use RM\TaggedEntityTrait;
    use TreeTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Cms\PageInterface
     */
    protected $parent;

    /**
     * @var ArrayCollection|Cms\PageInterface[]
     */
    protected $children;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var bool
     */
    protected $static;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var bool
     */
    protected $advanced;

    /**
     * @var bool
     */
    protected $dynamicPath;

    /**
     * @var bool
     */
    protected $enabled;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->children = new ArrayCollection();

        $this->static = false;
        $this->locked = false;
        $this->advanced = false;
        $this->dynamicPath = false;
        $this->enabled = true;
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?: 'New page';
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
    public function setParent(Cms\PageInterface $parent = null): Cms\PageInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParent(): ?Cms\PageInterface
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function hasChild(Cms\PageInterface $child): bool
    {
        return $this->children->contains($child);
    }

    /**
     * @inheritdoc
     */
    public function addChild(Cms\PageInterface $child): Cms\PageInterface
    {
        if (!$this->hasChild($child)) {
            $this->children->add($child);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeChild(Cms\PageInterface $child): Cms\PageInterface
    {
        if ($this->hasChild($child)) {
            $this->children->removeElement($child);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasChildren(): bool
    {
        return 0 < $this->children->count();
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): Cms\PageInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): Cms\PageInterface
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function setBreadcrumb(string $breadcrumb): Cms\PageInterface
    {
        $this->translate()->setBreadcrumb($breadcrumb);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBreadcrumb(): ?string
    {
        return $this->translate()->getBreadcrumb();
    }

    /**
     * @inheritdoc
     */
    public function setHtml(string $html = null): Cms\PageInterface
    {
        $this->translate()->setHtml($html);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml(): ?string
    {
        return $this->translate()->getHtml();
    }

    /**
     * @inheritdoc
     */
    public function setPath(string $path = null): Cms\PageInterface
    {
        $this->translate()->setPath($path);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath(): ?string
    {
        return $this->translate()->getPath();
    }

    /**
     * @inheritdoc
     */
    public function setRoute(string $route = null): Cms\PageInterface
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @inheritdoc
     */
    public function setStatic(bool $static): Cms\PageInterface
    {
        $this->static = $static;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

    /**
     * @inheritdoc
     */
    public function setLocked(bool $locked): Cms\PageInterface
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @inheritdoc
     */
    public function setController(string $controller = null): Cms\PageInterface
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @inheritdoc
     */
    public function setAdvanced(bool $advanced): Cms\PageInterface
    {
        $this->advanced = $advanced;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAdvanced(): bool
    {
        return $this->advanced;
    }

    /**
     * @inheritdoc
     */
    public function setDynamicPath(bool $dynamicPath): Cms\PageInterface
    {
        $this->dynamicPath = $dynamicPath;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isDynamicPath(): bool
    {
        return $this->dynamicPath;
    }

    /**
     * @inheritdoc
     */
    public function setEnabled(bool $enabled): Cms\PageInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @inheritdoc
     */
    public function isIndexable(): bool
    {
        return $this->enabled && $this->seo->getIndex();
    }

    /**
     * @inheritdoc
     */
    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.page';
    }

    /**
     * @inheritdoc
     */
    public static function getRouteCacheTag(string $route): string
    {
        return "ekyna_cms.page[route:$route]";
    }
}
