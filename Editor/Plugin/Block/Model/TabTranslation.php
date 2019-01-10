<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;
use Ekyna\Component\Resource\Model\TranslationTrait;

/**
 * Class TabTranslation
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @var string
     */
    private $title;


    /**
     * @inheritDoc
     *
     * @TODO Remove
     */
    public function getId()
    {
        return null;
    }

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
