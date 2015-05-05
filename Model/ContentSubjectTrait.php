<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Entity\TinymceBlock;

/**
 * Class ContentSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ContentSubjectTrait
{
    /**
     * @var ArrayCollection|ContentInterface[]
     */
    protected $contents;

    /**
     * Returns the content (last version).
     *
     * @return ContentInterface|null
     */
    public function getContent()
    {
        if (null !== $this->contents && 0 < $this->contents->count()) {
            return $this->contents->first();
        }
        return null;
    }

    /**
     * Sets the contents.
     *
     * @param ArrayCollection $contents
     *
     * @return ContentSubjectInterface|$this
     */
    public function setContents(ArrayCollection $contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Adds the content.
     *
     * @param ContentInterface $content
     *
     * @return ContentSubjectInterface|$this
     */
    public function addContent(ContentInterface $content)
    {
        $this->contents->add($content);
    
        return $this;
    }

    /**
     * Removes the content.
     *
     * @param ContentInterface $content
     *
     * @return ContentSubjectInterface|$this
     */
    public function removeContent(ContentInterface $content)
    {
        $this->contents->removeElement($content);

        return $this;
    }

    /**
     * Returns the contents.
     *
     * @return ArrayCollection|ContentInterface[]
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Returns the content summary.
     *
     * @param int $maxLength
     * @return string
     */
    public function getContentSummary($maxLength = 128)
    {
        if (null !== $content = $this->getContent()) {
            $length = 0;
            $blockContents = [];
            foreach ($content->getBlocks() as $block) {
                if ($block instanceof TinymceBlock) {
                    $temp = strip_tags($block->getHtml());
                    $tempLength = strlen($temp);
                    if ($length + $tempLength >= $maxLength) {
                        $temp = substr($temp, 0, $maxLength - ($length + $tempLength));
                        $blockContents[] = $temp;
                        break;
                    } else {
                        $blockContents[] = $temp;
                    }
                }
            }
            if (!empty($blockContents)) {
                return implode('<br>', $blockContents);
            }
        }
        return '';
    }
}
