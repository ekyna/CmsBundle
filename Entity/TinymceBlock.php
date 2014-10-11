<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

/**
 * Class TinymceBlock
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TinymceBlock extends AbstractBlock
{
    /**
     * @var string
     */
    private $html;


    /**
     * Set html
     *
     * @param string $html
     * @return TinymceBlock
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string 
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'tinymce';
    }
}
