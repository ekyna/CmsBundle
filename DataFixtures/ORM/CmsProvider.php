<?php

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Ekyna\Bundle\CmsBundle\Model\TagsSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Bundle\CoreBundle\DataFixtures\ORM\Fixtures;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface;

/**
 * Class CmsProvider
 * @package Ekyna\Bundle\CmsBundle\DataFixtures\ORM
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CmsProvider
{
    /**
     * @var ResourceRepositoryInterface
     */
    private $tagRepository;


    /**
     * Constructor.
     *
     * @param ResourceRepositoryInterface $tagRepository
     */
    public function __construct(ResourceRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Returns html paragraphs.
     *
     * @param int $min
     * @param int $max
     *
     * @return string
     */
    public function htmlParagraphs(int $min = 2, int $max = 5): string
    {
        $paragraphs = [];

        // Each paragraph
        for ($i = 0; $i < rand($min, $max); $i++) {
            $sentences = [];

            // Each sentence
            for ($j = 0; $j < rand(3, 7); $j++) {
                $nb = rand(5, 9);
                $words = Fixtures::getFaker()->words($nb, false);
                $words[0] = ucwords($words[0]);

                if (rand(0, 100) < 20) { // strong or em
                    $tag = rand(0, 100) > 50 ? 'strong' : 'em';
                    $start = 0;
                    $end = count($words) - 1;
                    if (rand(0, 100) > 50) {
                        $start = rand(0, $nb - 2);
                        $end = rand($start, $nb - 1);
                    }
                    $words[$start] = "<$tag>" . $words[$start];
                    $words[$end] = $words[$end] . "</$tag>";
                }

                $sentences[] = implode(' ', $words) . '.';
            }

            $paragraphs[] = implode(' ', $sentences);
        }

        return '<p>' . implode('</p><p>', $paragraphs) . '</p>';
    }

    /**
     * Returns a random theme.
     *
     * @return string
     */
    public function randomTheme(): string
    {
        return Fixtures::getFaker()->randomElement(Themes::getConstants());
    }

    /**
     * Generates the subject tags.
     *
     * @param TagsSubjectInterface $subject
     */
    public function generateTags(TagsSubjectInterface $subject): void
    {
        /** @var \Ekyna\Bundle\CmsBUndle\Model\TagInterface $tag */
        if (0 < $count = rand(0, 2)) {
            if (1 == $count) {
                if (null !== $tag = $this->tagRepository->findRandomOneBy([])) {
                    $subject->addTag($tag);
                }
            } else {
                $tags = $this->tagRepository->findRandomBy([], $count);
                foreach ($tags as $tag) {
                    $subject->addTag($tag);
                }
            }
        }
    }
}
