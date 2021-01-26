<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CoreBundle\Model\TreeInterface;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class PageInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method PageTranslationInterface translate($locale = null, $create = false)
 * @method PageTranslationInterface[] getTranslations()
 */
interface PageInterface extends
    ContentSubjectInterface,
    SeoSubjectInterface,
    RM\TimestampableInterface,
    RM\TaggedEntityInterface,
    RM\TranslatableInterface,
    TreeInterface
{
    /**
     * Set parent
     *
     * @param PageInterface|null $parent
     *
     * @return PageInterface|$this
     */
    public function setParent(PageInterface $parent = null): PageInterface;

    /**
     * Get parent
     *
     * @return PageInterface|null
     */
    public function getParent(): ?PageInterface;

    /**
     * Returns whether the page has the child or not.
     *
     * @param PageInterface $child
     *
     * @return bool
     */
    public function hasChild(PageInterface $child): bool;

    /**
     * Add children
     *
     * @param PageInterface $child
     *
     * @return PageInterface|$this
     */
    public function addChild(PageInterface $child): PageInterface;

    /**
     * Remove children
     *
     * @param PageInterface $child
     *
     * @return PageInterface|$this
     */
    public function removeChild(PageInterface $child): PageInterface;

    /**
     * Has children
     *
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * Get children
     *
     * @return Collection|PageInterface[]
     */
    public function getChildren(): Collection;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PageInterface|$this
     */
    public function setName(string $name): PageInterface;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return PageInterface|$this
     */
    public function setTitle(string $title): PageInterface;

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Set breadcrumb
     *
     * @param string $breadcrumb
     *
     * @return PageInterface|$this
     */
    public function setBreadcrumb(string $breadcrumb): PageInterface;

    /**
     * Get breadcrumb
     *
     * @return string
     */
    public function getBreadcrumb(): ?string;

    /**
     * Set html
     *
     * @param string|null $html
     *
     * @return PageInterface|$this
     */
    public function setHtml(string $html = null): PageInterface;

    /**
     * Return html
     *
     * @return string
     */
    public function getHtml(): ?string;

    /**
     * Set path
     *
     * @param string|null $path
     *
     * @return PageInterface|$this
     */
    public function setPath(string $path = null): PageInterface;

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): ?string;

    /**
     * Set route
     *
     * @param string|null $route
     *
     * @return PageInterface|$this
     */
    public function setRoute(string $route = null): PageInterface;

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute(): ?string;

    /**
     * Set static
     *
     * @param bool $static
     *
     * @return PageInterface|$this
     */
    public function setStatic(bool $static): PageInterface;

    /**
     * Get static
     *
     * @return bool
     */
    public function isStatic(): bool;

    /**
     * Set locked
     *
     * @param bool $locked
     *
     * @return PageInterface|$this
     */
    public function setLocked(bool $locked): PageInterface;

    /**
     * Get locked
     *
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * Set controller
     *
     * @param string|null $controller
     *
     * @return PageInterface|$this
     */
    public function setController(string $controller = null): PageInterface;

    /**
     * Get controller
     *
     * @return string
     */
    public function getController(): ?string;

    /**
     * Set advanced
     *
     * @param bool $advanced
     *
     * @return PageInterface|$this
     */
    public function setAdvanced(bool $advanced): PageInterface;

    /**
     * Get advanced
     *
     * @return bool
     */
    public function isAdvanced(): bool;

    /**
     * Sets the dynamic path.
     *
     * @param bool $dynamicPath
     *
     * @return PageInterface|$this
     */
    public function setDynamicPath(bool $dynamicPath): PageInterface;

    /**
     * Returns the dynamic path.
     *
     * @return bool
     */
    public function isDynamicPath(): bool;

    /**
     * Sets the enabled.
     *
     * @param bool $enabled
     *
     * @return PageInterface|$this
     */
    public function setEnabled(bool $enabled): PageInterface;

    /**
     * Returns the enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Returns whether the page should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable(): bool;

    /**
     * Returns the page route cache tag.
     *
     * @param string $route
     *
     * @return string
     */
    public static function getRouteCacheTag(string $route): string;
}
