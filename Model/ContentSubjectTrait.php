<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ContentSubjectTrait
 */
trait ContentSubjectTrait
{
    /**
     * @var ArrayCollection
     */
    protected $contents;

    /**
     * Get content
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\AbstractContent
     */
    public function getContent()
    {
        if(0 < $this->contents->count()) {
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
