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
     * Returns whether the exhibitor should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable()
    {
        return true;
    }

    /**
     * Returns the indexable content.
     *
     * @return string
     */
    public function getIndexableContent()
    {
        return strip_tags($this->html);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'tinymce';
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.tinymce_block';
    }
}
