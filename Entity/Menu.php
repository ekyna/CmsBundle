<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model as Cms;
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
    use RM\TreeTrait;
    use RM\TaggedEntityTrait;

    protected ?string            $name        = null;
    protected ?string            $description = null;
    protected ?string            $route       = null;
    protected array              $parameters;
    protected array              $attributes;
    protected bool               $locked;
    protected bool               $enabled;
    protected array              $options;
    protected ?Cms\PageInterface $page        = null;


    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->initializeNode();

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
     * @inheritDoc
     */
    public function setName(string $name): Cms\MenuInterface
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
    public function setDescription(string $description = null): Cms\MenuInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setRoute(string $route = null): Cms\MenuInterface
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
    public function setParameters(array $parameters = []): Cms\MenuInterface
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $attributes = []): Cms\MenuInterface
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function setLocked(bool $locked): Cms\MenuInterface
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
    public function setEnabled(bool $enabled): Cms\MenuInterface
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
    public function getOption(string $key)
    {
        return $this->options[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): Cms\MenuInterface
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title): Cms\MenuInterface
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
    public function setPath(string $path = null): Cms\MenuInterface
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
    public function setPage(Cms\PageInterface $page): Cms\MenuInterface
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPage(): ?CMS\PageInterface
    {
        return $this->page;
    }

    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.menu';
    }
}
