<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\AdminBundle\Model\AbstractTranslation;
use Ekyna\Bundle\CmsBundle\Model\BlockTranslationInterface;

/**
 * Class TinymceBlockTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TinymceBlockTranslation extends AbstractTranslation implements BlockTranslationInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $html;


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the html.
     *
     * @param string $html
     * @return TinymceBlockTranslation
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Returns the html.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }
}
