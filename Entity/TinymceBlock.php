<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

/**
 * TinymceBlock
 */
class TinymceBlock extends Block
{
    /**
     * @var string
     */
    private $html;


    /**
     * Set html
     *
     * @param string $html
     * @return Text
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
