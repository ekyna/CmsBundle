<?php

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
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $path;


    /**
     * @inheritdoc
     */
    public function setTitle(string $title): MenuTranslationInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setPath(string $path = null): MenuTranslationInterface
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
}
