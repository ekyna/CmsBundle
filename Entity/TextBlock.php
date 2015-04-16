<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

/**
 * Class TextBlock
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TextBlock extends AbstractBlock
{
    /**
     * @var string
     */
    private $text;


    /**
     * Set text
     *
     * @param string $text
     * @return TextBlock
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.text_block';
    }
}
