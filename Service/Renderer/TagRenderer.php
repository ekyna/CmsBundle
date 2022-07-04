<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model\TagInterface;
use Ekyna\Bundle\CmsBundle\Model\TagsSubjectInterface;
use InvalidArgumentException;

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
     * @param TagsSubjectInterface|Collection<TagInterface>|array<TagInterface> $subjectOrTags
     */
    public function renderTags(TagsSubjectInterface|Collection|array $subjectOrTags, array $options = []): string
    {
        if ($subjectOrTags instanceof TagsSubjectInterface) {
            $tags = $subjectOrTags->getTags();
        } elseif ($subjectOrTags instanceof Collection) {
            $tags = new ArrayCollection($subjectOrTags->getValues());
        } elseif (is_array($subjectOrTags)) {
            $tags = new ArrayCollection($subjectOrTags);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected instance of %s, %s or array of %s.',
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
     */
    private function getRenderer(array $options): callable
    {
        if ($options['text']) {
            if ($options['badge']) {
                return function (TagInterface $tag) {
                    return sprintf(
                        '<span class="label label-%s"><i class="fa fa-%s"></i> %s</span>',
                        $tag->getTheme(), $tag->getIcon(), $tag->getName()
                    );
                };
            }

            return function (TagInterface $tag) {
                return sprintf(
                    '<span class="text-%s"><i class="fa fa-%s"></i> %s</span>',
                    $tag->getTheme(), $tag->getIcon(), $tag->getName()
                );
            };
        }

        if ($options['badge']) {
            return function (TagInterface $tag) {
                return sprintf(
                    '<span class="label label-%s" title="%s"><i class="fa fa-%s"></i></span>',
                    $tag->getTheme(), $tag->getName(), $tag->getIcon()
                );
            };
        }

        return function (TagInterface $tag) {
            return sprintf(
                '<i class="text-%s fa fa-%s" title="%s"></i>',
                $tag->getTheme(), $tag->getIcon(), $tag->getName()
            );
        };
    }
}
