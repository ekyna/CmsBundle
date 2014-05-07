<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ContentSubjectTrait
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ContentSubjectTrait
{
    /**
     * @var string
     */
    protected $html;

    /**
     * @var ArrayCollection
     */
    protected $contents;

    /**
     * Set html
     *
     * @param string $html
     * @return ContentSubjectInterface
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
     * Get content
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\AbstractContent
     */
    public function getContent()
    {
        if(null !== $this->contents && 0 < $this->contents->count()) {
            return $this->contents->first();
        }
        return null;
    }

    public function setContents(ArrayCollection $contents)
    {
        $this->contents = $contents;

        return $this;
    }

    public function addContent(Content $content)
    {
        $this->contents->add($content);
    
        return $this;
    }

    public function removeContent(Content $content)
    {
        $this->contents->removeElement($content);
    }

    public function getContents()
    {
        return $this->contents;
    }
}
