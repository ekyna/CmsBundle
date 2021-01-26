<?php

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
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $breadcrumb;

    /**
     * @var string
     */
    protected $html;

    /**
     * @var string
     */
    protected $path;


    /**
     * @inheritdoc
     */
    public function setTitle(string $title): PageTranslationInterface
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
    public function setBreadcrumb(string $breadcrumb): PageTranslationInterface
    {
        $this->breadcrumb = $breadcrumb;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBreadcrumb(): ?string
    {
        return $this->breadcrumb;
    }

    /**
     * @inheritdoc
     */
    public function setHtml(string $html = null): PageTranslationInterface
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * @inheritdoc
     */
    public function setPath(string $path = null): PageTranslationInterface
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
