<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

/**
 * Ekyna\Bundle\CmsBundle\Entity$TextBlock
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TextBlock extends Block
{
    /**
     * @var string
     */
    private $text;


    /**
     * Set text
     *
     * @param string $text
     * @return Text
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
}
