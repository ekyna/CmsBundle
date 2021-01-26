<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model\TreeTrait;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Menu
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Cms\MenuTranslationInterface translate($locale = null, $create = false)
 * @method Cms\MenuTranslationInterface[] getTranslations()
 */
class Menu extends RM\AbstractTranslatable implements Cms\MenuInterface
{
    use RM\TaggedEntityTrait;
    use TreeTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Menu
     */
    protected $parent;

    /**
     * @var int
     */
    protected $left;

    /**
     * @var int
     */
    protected $right;

    /**
     * @var int
     */
    protected $root;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var Collection|Menu[]
     */
    protected $children;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var Cms\PageInterface
     */
    protected $page;


    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->children = new ArrayCollection();
        $this->parameters = [];
        $this->attributes = [];
        $this->options = [];
        $this->locked = false;
        $this->enabled = true;
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTitle() ?: 'New menu';
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
    public function setParent(Cms\MenuInterface $parent = null): Cms\MenuInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParent(): ?Cms\MenuInterface
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function setChildren(Collection $children): Cms\MenuInterface
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasChildren(): bool
    {
        return 0 < count($this->children);
    }

    /**
     * @inheritdoc
     */
    public function addChild(Cms\MenuInterface $menu): Cms\MenuInterface
    {
        $this->children[] = $menu;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeChild(Cms\MenuInterface $menu): Cms\MenuInterface
    {
        $this->children->removeElement($menu);

        return $this;
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
    public function setName(string $name): Cms\MenuInterface
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
    public function setDescription(string $description = null): Cms\MenuInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setRoute(string $route = null): Cms\MenuInterface
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
    public function setParameters(array $parameters = []): Cms\MenuInterface
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function setAttributes(array $attributes = []): Cms\MenuInterface
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function setLocked(bool $locked): Cms\MenuInterface
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
    public function setEnabled(bool $enabled): Cms\MenuInterface
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
    public function getOption(string $key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options): Cms\MenuInterface
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): Cms\MenuInterface
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): ?string
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function setPath(string $path = null): Cms\MenuInterface
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
    public function setPage(Cms\PageInterface $page): Cms\MenuInterface
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPage(): ?CMS\PageInterface
    {
        return $this->page;
    }

    /**
     * @inheritdoc
     */
    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.menu';
    }
}
