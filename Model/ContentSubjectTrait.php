<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\TinymceBlock;

/**
 * Class ContentSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ContentSubjectTrait
{
    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * Sets the content.
     *
     * @param ContentInterface $content
     *
     * @return ContentSubjectInterface|$this
     */
    public function setContent(ContentInterface $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns the content.
     *
     * @return ContentInterface|null
     */
    public function getContent()
    {
        return $this->content;
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
