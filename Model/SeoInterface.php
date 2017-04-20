<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface SeoInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method SeoTranslationInterface translate($locale = null, $create = false)
 * @method Collection|SeoTranslationInterface[] getTranslations()
 */
interface SeoInterface extends RM\TaggedEntityInterface, RM\TranslatableInterface
{
    /**
     * Sets the (translated) title.
     *
     * @param string|null $title
     *
     * @return SeoInterface|$this
     */
    public function setTitle(string $title = null): SeoInterface;

    /**
     * Returns the (translated) title.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Set description
     *
     * @param string|null $description
     *
     * @return SeoInterface|$this
     */
    public function setDescription(string $description = null): SeoInterface;

    /**
     * Returns the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Set changefreq
     *
     * @param string $changefreq
     *
     * @return SeoInterface|$this
     */
    public function setChangefreq(string $changefreq): SeoInterface;

    /**
     * Returns the change frequency.
     *
     * @return string
     */
    public function getChangefreq(): string;

    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return SeoInterface|$this
     */
    public function setPriority(string $priority): SeoInterface;

    /**
     * Returns the priority.
     *
     * @return string
     */
    public function getPriority(): string;

    /**
     * Returns the follow.
     *
     * @return bool
     */
    public function getFollow(): bool;

    /**
     * Sets the follow.
     *
     * @param bool $follow
     *
     * @return SeoInterface|$this
     */
    public function setFollow(bool $follow): SeoInterface;

    /**
     * Returns the index.
     *
     * @return bool
     */
    public function getIndex(): bool;

    /**
     * Sets the index.
     *
     * @param bool $index
     *
     * @return SeoInterface|$this
     */
    public function setIndex(bool $index): SeoInterface;

    /**
     * Returns the canonical.
     *
     * @return string|null
     */
    public function getCanonical(): ?string;

    /**
     * Sets the canonical.
     *
     * @param string|null $canonical
     *
     * @return SeoInterface|$this
     */
    public function setCanonical(string $canonical = null): SeoInterface;

    /**
     * Returns whether the seo should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable(): bool;
}
