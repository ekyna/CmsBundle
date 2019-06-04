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
