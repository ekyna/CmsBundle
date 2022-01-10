<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class PageInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method PageTranslationInterface translate($locale = null, $create = false)
 * @method PageTranslationInterface[] getTranslations()
 * @method Collection|PageInterface[] getChildren()
 * @method PageInterface|null getParent()
 */
interface PageInterface extends
    ContentSubjectInterface,
    SeoSubjectInterface,
    RM\TreeInterface,
    RM\TimestampableInterface,
    RM\TaggedEntityInterface,
    RM\TranslatableInterface
{
    /**
     * Sets the name
     *
     * @param string $name
     *
     * @return PageInterface|$this
     */
    public function setName(string $name): PageInterface;

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Sets the (translated) title.
     *
     * @param string|null $title
     *
     * @return PageInterface|$this
     */
    public function setTitle(string $title = null): PageInterface;

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Sets the (translated) breadcrumb.
     *
     * @param string|null $breadcrumb
     *
     * @return PageInterface|$this
     */
    public function setBreadcrumb(string $breadcrumb = null): PageInterface;

    /**
     * Returns the breadcrumb
     *
     * @return string
     */
    public function getBreadcrumb(): ?string;

    /**
     * Sets the (translated) html.
     *
     * @param string|null $html
     *
     * @return PageInterface|$this
     */
    public function setHtml(string $html = null): PageInterface;

    /**
     * Returns the html
     *
     * @return string
     */
    public function getHtml(): ?string;

    /**
     * Sets the (translated) path.
     *
     * @param string|null $path
     *
     * @return PageInterface|$this
     */
    public function setPath(string $path = null): PageInterface;

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath(): ?string;

    /**
     * Sets the route
     *
     * @param string|null $route
     *
     * @return PageInterface|$this
     */
    public function setRoute(string $route = null): PageInterface;

    /**
     * Returns the route
     *
     * @return string
     */
    public function getRoute(): ?string;

    /**
     * Sets whether this page is static
     *
     * @param bool $static
     *
     * @return PageInterface|$this
     */
    public function setStatic(bool $static): PageInterface;

    /**
     * Returns whether this page is static
     *
     * @return bool
     */
    public function isStatic(): bool;

    /**
     * Sets whether this page is locked
     *
     * @param bool $locked
     *
     * @return PageInterface|$this
     */
    public function setLocked(bool $locked): PageInterface;

    /**
     * Returns whether this page is locked
     *
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * Sets the controller
     *
     * @param string|null $controller
     *
     * @return PageInterface|$this
     */
    public function setController(string $controller = null): PageInterface;

    /**
     * Returns the controller
     *
     * @return string
     */
    public function getController(): ?string;

    /**
     * Sets whether this page is advanced
     *
     * @param bool $advanced
     *
     * @return PageInterface|$this
     */
    public function setAdvanced(bool $advanced): PageInterface;

    /**
     * Returns whether this page is advanced
     *
     * @return bool
     */
    public function isAdvanced(): bool;

    /**
     * Sets whether this page has dynamic path.
     *
     * @param bool $dynamicPath
     *
     * @return PageInterface|$this
     */
    public function setDynamicPath(bool $dynamicPath): PageInterface;

    /**
     * Returns whether this page has dynamic path.
     *
     * @return bool
     */
    public function isDynamicPath(): bool;

    /**
     * Sets whether this page is enabled.
     *
     * @param bool $enabled
     *
     * @return PageInterface|$this
     */
    public function setEnabled(bool $enabled): PageInterface;

    /**
     * Returns whether this page is enabled.
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
     * @param string $locale
     *
     * @return string
     */
    public static function getRouteCacheTag(string $route, string $locale): string;
}
