<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface ContentSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentSubjectInterface
{
    /**
     * Sets the content.
     *
     * @param ContentInterface $content
     * @return ContentSubjectInterface|$this
     */
    public function setContent(ContentInterface $content);

    /**
     * Returns the current content (last version).
     *
     * @return ContentInterface|null
     */
    public function getContent();

    /**
     * Returns the content summary.
     *
     * @param int $maxLength
     * @return string
     */
    public function getContentSummary($maxLength = 128);
}
