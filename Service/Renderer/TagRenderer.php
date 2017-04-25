<?php

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model\TagInterface;
use Ekyna\Bundle\CmsBundle\Model\TagsSubjectInterface;

/**
 * Class TagRenderer
 * @package Ekyna\Bundle\CmsBundle\Service\Renderer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TagRenderer
{
    /**
     * Renders the tags.
     *
     * @param TagsSubjectInterface|Collection|TagInterface[] $subjectOrTags
     * @param array                                          $options
     *
     * @return string
     */
    public function renderTags($subjectOrTags, array $options = [])
    {
        if ($subjectOrTags instanceof TagsSubjectInterface) {
            $tags = $subjectOrTags->getTags();
        } elseif ($subjectOrTags instanceof Collection) {
            $tags = new ArrayCollection($subjectOrTags->getValues());
        } elseif (is_array($subjectOrTags)) {
            $tags = new ArrayCollection($subjectOrTags);
        } else {
            throw new \InvalidArgumentException(sprintf(
                "Expected instance of %s, %s or array of %s.",
                TagsSubjectInterface::class,
                Collection::class,
                TagInterface::class
            ));
        }

        if (0 == $tags->count()) {
            return '';
        }

        $options = array_replace([
            'text'  => true,
            'badge' => true,
        ], $options);

        $output = '';

        $renderer = $this->getRenderer($options);

        foreach ($tags as $tag) {
            $output .= $renderer($tag);
        }

        return $output;
    }

    /**
     * Returns the renderer.
     *
     * @param array $options
     *
     * @return \Closure
     */
    private function getRenderer(array $options)
    {
        if ($options['text']) {
            if ($options['badge']) {
                return function (TagInterface $tag) {
                    return sprintf(
                        '<span class="label label-%s"><span class="fa fa-%s"></span> %s</span>',
                        $tag->getTheme(), $tag->getIcon(), $tag->getName()
                    );
                };
            } else {
                return function (TagInterface $tag) {
                    return sprintf(
                        '<span class="text-%s"><span class="fa fa-%s"></span> %s</span>',
                        $tag->getTheme(), $tag->getIcon(), $tag->getName()
                    );
                };
            }
        } else {
            if ($options['badge']) {
                return function (TagInterface $tag) {
                    return sprintf(
                        '<span class="label label-%s" title="%s"><span class="fa fa-%s"></span></span>',
                        $tag->getTheme(), $tag->getName(), $tag->getIcon()
                    );
                };
            } else {
                return function (TagInterface $tag) {
                    return sprintf(
                        '<span class="text-%s fa fa-%s" title="%s"></span>',
                        $tag->getTheme(), $tag->getIcon(), $tag->getName()
                    );
                };
            }
        }
    }
}
