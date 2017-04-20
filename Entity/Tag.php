<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\TagInterface;
use Ekyna\Bundle\CmsBundle\Model\Themes;

/**
 * Class Tag
 * @package      Ekyna\Bundle\CmsBundle\Entity
 * @author       Etienne Dauvergne <contact@ekyna.com>
 */
class Tag implements TagInterface
{
    private ?int    $id    = null;
    private ?string $name  = null;
    private string  $theme = Themes::THEME_DEFAULT;
    private ?string $icon  = null;


    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?: 'New tag';
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name = null): TagInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function setIcon(string $icon = null): TagInterface
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @inheritDoc
     */
    public function setTheme(string $theme): TagInterface
    {
        $this->theme = $theme;

        return $this;
    }
}
