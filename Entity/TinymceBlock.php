<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\AdminBundle\Model\TranslatableInterface;
use Ekyna\Bundle\AdminBundle\Model\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class TinymceBlock
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method \Ekyna\Bundle\CmsBundle\Entity\TinymceBlockTranslation translate($locale = null, $create = false)
 */
class TinymceBlock extends AbstractBlock implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Set html
     *
     * @param string $html
     * @return TinymceBlock
     */
    public function setHtml($html)
    {
        $this->translate()->setHtml($html);

        return $this;
    }

    /**
     * Get html
     *
     * @return string 
     */
    public function getHtml()
    {
        return $this->translate()->getHtml();
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
        return strip_tags($this->getHtml());
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
