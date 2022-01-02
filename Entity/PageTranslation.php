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

    public function setTitle(string $title = null): PageTranslationInterface
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setBreadcrumb(string $breadcrumb = null): PageTranslationInterface
    {
        $this->breadcrumb = $breadcrumb;

        return $this;
    }

    public function getBreadcrumb(): ?string
    {
        return $this->breadcrumb;
    }

    public function setHtml(string $html = null): PageTranslationInterface
    {
        $this->html = $html;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setPath(string $path = null): PageTranslationInterface
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}
