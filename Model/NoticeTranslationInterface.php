<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\NoticeTranslation;
use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface NoticeTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface NoticeTranslationInterface extends TranslationInterface
{
    /**
     * Returns the content.
     *
     * @return string
     */
    public function getContent(): ?string;

    /**
     * Sets the content.
     *
     * @param string $content
     *
     * @return NoticeTranslation
     */
    public function setContent(string $content = null): NoticeTranslationInterface;
}
