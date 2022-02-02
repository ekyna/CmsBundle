<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\TagInterface;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Component\Resource\Model\AbstractResource;

/**
 * Class Tag
 * @package      Ekyna\Bundle\CmsBundle\Entity
 * @author       Etienne Dauvergne <contact@ekyna.com>
 */
class Tag extends AbstractResource implements TagInterface
{
    private ?string $name  = null;
    private string  $theme = Themes::THEME_DEFAULT;
    private ?string $icon  = null;

    public function __toString(): string
    {
        return $this->name ?: 'New tag';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name = null): TagInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon = null): TagInterface
    {
        $this->icon = $icon;

        return $this;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): TagInterface
    {
        $this->theme = $theme;

        return $this;
    }
}
