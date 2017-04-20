<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model as Cms;
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
    use RM\TreeTrait;


    protected ?int    $id         = null;
    protected ?string $name       = null;
    protected ?string $route      = null;
    protected bool    $static;
    protected bool    $locked;
    protected ?string $controller = null;
    protected bool    $advanced;
    protected bool    $dynamicPath;
    protected bool    $enabled;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->initializeNode();

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
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): Cms\PageInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title = null): Cms\PageInterface
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function setBreadcrumb(string $breadcrumb = null): Cms\PageInterface
    {
        $this->translate()->setBreadcrumb($breadcrumb);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBreadcrumb(): ?string
    {
        return $this->translate()->getBreadcrumb();
    }

    /**
     * @inheritDoc
     */
    public function setHtml(string $html = null): Cms\PageInterface
    {
        $this->translate()->setHtml($html);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): ?string
    {
        return $this->translate()->getHtml();
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path = null): Cms\PageInterface
    {
        $this->translate()->setPath($path);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): ?string
    {
        return $this->translate()->getPath();
    }

    /**
     * @inheritDoc
     */
    public function setRoute(string $route = null): Cms\PageInterface
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @inheritDoc
     */
    public function setStatic(bool $static): Cms\PageInterface
    {
        $this->static = $static;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

    /**
     * @inheritDoc
     */
    public function setLocked(bool $locked): Cms\PageInterface
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @inheritDoc
     */
    public function setController(string $controller = null): Cms\PageInterface
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @inheritDoc
     */
    public function setAdvanced(bool $advanced): Cms\PageInterface
    {
        $this->advanced = $advanced;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAdvanced(): bool
    {
        return $this->advanced;
    }

    /**
     * @inheritDoc
     */
    public function setDynamicPath(bool $dynamicPath): Cms\PageInterface
    {
        $this->dynamicPath = $dynamicPath;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDynamicPath(): bool
    {
        return $this->dynamicPath;
    }

    /**
     * @inheritDoc
     */
    public function setEnabled(bool $enabled): Cms\PageInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @inheritDoc
     */
    public function isIndexable(): bool
    {
        return $this->enabled && $this->seo->getIndex();
    }

    /**
     * @inheritDoc
     */
    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.page';
    }

    /**
     * @inheritDoc
     */
    public static function getRouteCacheTag(string $route): string
    {
        return "ekyna_cms.page[route=$route]";
    }
}
