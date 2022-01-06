<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\SeoTranslationInterface;
use Ekyna\Component\Resource\Copier\CopierInterface;
use Ekyna\Component\Resource\Copier\CopyInterface;
use Ekyna\Component\Resource\Model\AbstractTranslation;

/**
 * Class SeoTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoTranslation extends AbstractTranslation implements SeoTranslationInterface, CopyInterface
{
    protected ?string $title       = null;
    protected ?string $description = null;
    protected ?string $keywords    = null;

    public function onCopy(CopierInterface $copier): void
    {
        $this->title = null;
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title = null): SeoTranslationInterface
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
    public function setDescription(string $description = null): SeoTranslationInterface
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
    public function setKeywords(string $keywords = null): SeoTranslationInterface
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->title);
    }
}
