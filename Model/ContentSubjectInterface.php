<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface;

/**
 * Interface ContentSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentSubjectInterface
{
    /**
     * Sets the content.
     *
     * @param ContentInterface|null $content
     *
     * @return ContentSubjectInterface|$this
     */
    public function setContent(ContentInterface $content = null): ContentSubjectInterface;

    /**
     * Returns the current content (last version).
     *
     * @return ContentInterface|null
     */
    public function getContent(): ?ContentInterface;
}
