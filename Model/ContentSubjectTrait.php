<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface;

/**
 * Class ContentSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
trait ContentSubjectTrait
{
    protected ?ContentInterface $content = null;

    /**
     * Sets the content.
     *
     * @param ContentInterface|null $content
     *
     * @return ContentSubjectInterface|$this
     */
    public function setContent(ContentInterface $content = null): ContentSubjectInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns the content.
     *
     * @return ContentInterface|null
     */
    public function getContent(): ?ContentInterface
    {
        return $this->content;
    }
}
