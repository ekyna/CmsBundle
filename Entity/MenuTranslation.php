<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\MenuTranslationInterface;
use Ekyna\Component\Resource\Model\AbstractTranslation;

/**
 * Class MenuTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuTranslation extends AbstractTranslation implements MenuTranslationInterface
{
    protected ?string $title = null;
    protected ?string $path  = null;


    /**
     * @inheritDoc
     */
    public function setTitle(string $title = null): MenuTranslationInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path = null): MenuTranslationInterface
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
}
