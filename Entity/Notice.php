<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use DateTime;
use Ekyna\Bundle\CmsBundle\Model\NoticeInterface;
use Ekyna\Bundle\CmsBundle\Model\NoticeTranslationInterface;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Component\Resource\Model\AbstractTranslatable;

/**
 * Class Notice
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @method NoticeTranslationInterface translate($locale = null, $create = false)
 * @method NoticeTranslationInterface[] getTranslations()
 */
class Notice extends AbstractTranslatable implements NoticeInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var DateTime
     */
    private $startAt;

    /**
     * @var DateTime
     */
    private $endAt;


    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->theme = Themes::THEME_DEFAULT;
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?: 'New notice';
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
    public function setName(string $name): NoticeInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @inheritDoc
     */
    public function setTheme(string $theme): NoticeInterface
    {
        $this->theme = $theme;

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
    public function setIcon(string $icon = null): NoticeInterface
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStartAt(): ?DateTime
    {
        return $this->startAt;
    }

    /**
     * @inheritDoc
     */
    public function setStartAt(DateTime $date): NoticeInterface
    {
        $this->startAt = $date;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEndAt(): ?DateTime
    {
        return $this->endAt;
    }

    /**
     * @inheritDoc
     */
    public function setEndAt(DateTime $date): NoticeInterface
    {
        $this->endAt = $date;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContent(string $content = null): NoticeInterface
    {
        $this->translate()->setContent($content);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): ?string
    {
        return $this->translate()->getContent();
    }
}
