<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use DateTime;
use Ekyna\Component\Resource\Model\TranslatableInterface;

/**
 * Interface NoticeInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface NoticeInterface extends TranslatableInterface
{
    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return NoticeInterface
     */
    public function setName(string $name): NoticeInterface;

    /**
     * Returns the theme.
     *
     * @return string
     */
    public function getTheme(): string;

    /**
     * Sets the theme.
     *
     * @param string $theme
     *
     * @return NoticeInterface
     */
    public function setTheme(string $theme): NoticeInterface;

    /**
     * Returns the icon.
     *
     * @return string
     */
    public function getIcon(): ?string;

    /**
     * Sets the icon.
     *
     * @param string|null $icon
     *
     * @return NoticeInterface
     */
    public function setIcon(string $icon = null): NoticeInterface;

    /**
     * Returns the start date.
     *
     * @return DateTime
     */
    public function getStartAt(): ?DateTime;

    /**
     * Sets the start date.
     *
     * @param DateTime $date
     *
     * @return NoticeInterface
     */
    public function setStartAt(DateTime $date): NoticeInterface;

    /**
     * Returns the end date.
     *
     * @return DateTime
     */
    public function getEndAt(): ?DateTime;

    /**
     * Sets the end date.
     *
     * @param DateTime $date
     *
     * @return NoticeInterface
     */
    public function setEndAt(DateTime $date): NoticeInterface;

    /**
     * Sets the (translatable) content.
     *
     * @param string|null $content
     *
     * @return NoticeInterface
     */
    public function setContent(string $content = null): NoticeInterface;

    /**
     * Returns the (translatable) content.
     *
     * @return string
     */
    public function getContent(): ?string;
}
