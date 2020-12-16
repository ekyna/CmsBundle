<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\NoticeTranslationInterface;
use Ekyna\Component\Resource\Model\AbstractTranslation;

/**
 * Class NoticeTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeTranslation extends AbstractTranslation implements NoticeTranslationInterface
{
    /**
     * @var string
     */
    protected $content;


    /**
     * @inheritDoc
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function setContent(string $content = null): NoticeTranslationInterface
    {
        $this->content = $content;

        return $this;
    }
}
