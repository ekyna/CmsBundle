<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Ekyna\Bundle\CmsBUndle\Model\TagInterface;
use Ekyna\Bundle\CmsBundle\Model\TagsSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\Themes;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;
use Faker\Factory;
use Faker\Generator;

/**
 * Class CmsProvider
 * @package Ekyna\Bundle\CmsBundle\DataFixtures\ORM
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CmsProvider
{
    private ResourceRepositoryInterface $tagRepository;

    private ?Generator $faker = null;

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
        $faker = $this->getFaker();
        $paragraphs = [];

        // Each paragraph
        for ($i = 0; $i < rand($min, $max); $i++) {
            $sentences = [];

            // Each sentence
            for ($j = 0; $j < rand(3, 7); $j++) {
                $nb = rand(5, 9);
                $words = $faker->words($nb, false);
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
        return $this->getFaker()->randomElement(Themes::getConstants());
    }

    /**
     * Generates the subject tags.
     *
     * @param TagsSubjectInterface $subject
     */
    public function generateTags(TagsSubjectInterface $subject): void
    {
        /** @var TagInterface $tag */
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

    public function enabled(int $chance): bool
    {
        return rand(0, 100) <= $chance;
    }

    private function getFaker(): Generator
    {
        if ($this->faker) {
            return $this->faker;
        }

        return $this->faker = Factory::create();
    }
}
