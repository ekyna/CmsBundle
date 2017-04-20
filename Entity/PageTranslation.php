<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface;
use Ekyna\Component\Resource\Model\AbstractTranslation;

/**
 * Class PageTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageTranslation extends AbstractTranslation implements PageTranslationInterface
{
    protected ?string $title      = null;
    protected ?string $breadcrumb = null;
    protected ?string $html       = null;
    protected ?string $path       = null;


    /**
     * @inheritDoc
     */
    public function setTitle(string $title = null): PageTranslationInterface
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
    public function setBreadcrumb(string $breadcrumb = null): PageTranslationInterface
    {
        $this->breadcrumb = $breadcrumb;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBreadcrumb(): ?string
    {
        return $this->breadcrumb;
    }

    /**
     * @inheritDoc
     */
    public function setHtml(string $html = null): PageTranslationInterface
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path = null): PageTranslationInterface
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
