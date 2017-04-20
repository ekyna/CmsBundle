<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface MenuInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method MenuTranslationInterface translate($locale = null, $create = false)
 * @method MenuTranslationInterface[] getTranslations()
 * @method Collection|MenuInterface[] getChildren()
 * @method MenuInterface|null getParent()
 */
interface MenuInterface extends RM\TreeInterface, RM\TaggedEntityInterface, RM\TranslatableInterface
{
    /**
     * Set name
     *
     * @param string $name
     *
     * @return MenuInterface|$this
     */
    public function setName(string $name): MenuInterface;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Set description
     *
     * @param string|null $description
     *
     * @return MenuInterface|$this
     */
    public function setDescription(string $description = null): MenuInterface;

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * Sets the route.
     *
     * @param string|null $route
     *
     * @return MenuInterface|$this
     */
    public function setRoute(string $route = null): MenuInterface;

    /**
     * Returns the route.
     *
     * @return string
     */
    public function getRoute(): ?string;

    /**
     * Sets the route parameters.
     *
     * @param array $parameters
     *
     * @return MenuInterface|$this
     */
    public function setParameters(array $parameters = []): MenuInterface;

    /**
     * Returns the route parameters.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Sets the route attributes.
     *
     * @param array $attributes
     *
     * @return MenuInterface|$this
     */
    public function setAttributes(array $attributes = []): MenuInterface;

    /**
     * Returns the route attributes.
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Sets the locked.
     *
     * @param bool $locked
     *
     * @return MenuInterface|$this
     */
    public function setLocked(bool $locked): MenuInterface;

    /**
     * Returns the locked.
     *
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * Sets the enabled.
     *
     * @param bool $enabled
     *
     * @return MenuInterface|$this
     */
    public function setEnabled(bool $enabled): MenuInterface;

    /**
     * Returns the enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Returns the option for the given key.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getOption(string $key);

    /**
     * Sets the options.
     *
     * @param array $options
     *
     * @return MenuInterface|$this
     */
    public function setOptions(array $options): MenuInterface;

    /**
     * Returns the options.
     *
     * @return array
     */
    public function getOptions(): array;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return MenuInterface|$this
     */
    public function setTitle(string $title): MenuInterface;

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Sets the path.
     *
     * @param string|null $path
     *
     * @return MenuInterface|$this
     */
    public function setPath(string $path = null): MenuInterface;

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath(): ?string;

    /**
     * Sets the page (non mapped).
     *
     * @param PageInterface $page
     *
     * @return MenuInterface|$this
     */
    public function setPage(PageInterface $page): MenuInterface;

    /**
     * Returns the page (non mapped).
     *
     * @return PageInterface|null
     */
    public function getPage(): ?PageInterface;
}
