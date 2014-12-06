<?php

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Entity\Content;
use Ekyna\Bundle\CmsBundle\Entity\Seo;
use Ekyna\Bundle\CmsBundle\Entity\TinymceBlock;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\TagSubjectInterface;
use Faker\Factory;
use Nelmio\Alice\ProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CmsProcessor
 * @package Ekyna\Bundle\CmsBundle\DataFixtures\ORM
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsProcessor implements ProcessorInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $faker;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->faker = Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess($object)
    {
        if ($object instanceof SeoSubjectInterface) {
            $this->generateSeo($object);
        }
        if ($object instanceof ContentSubjectInterface) {
            $this->generateContent($object);
        }
        if ($object instanceof TagSubjectInterface) {
            $this->generateTags($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess($object)
    {

    }

    /**
     * Generates seo to the given subject.
     *
     * @param SeoSubjectInterface $subject
     */
    protected function generateSeo(SeoSubjectInterface $subject)
    {
        $seo = new Seo();
        if (0 < strlen($name = $this->objectToString($subject))) {
            $seo
                ->setTitle($name . ' seo title')
                ->setDescription($name . ' seo description')
            ;
        } else {
            $seo
                ->setTitle($this->faker->sentence(rand(3, 6)))
                ->setDescription($this->faker->words(rand(3, 6)))
            ;
        }
        $subject->setSeo($seo);
    }

    /**
     * Generates content to the given subject.
     *
     * @param ContentSubjectInterface $subject
     */
    protected function generateContent(ContentSubjectInterface $subject)
    {
        $html = '';
        for ($i = 0; $i < rand(3, 5); $i++) {
            $html .= '<p>' . $this->faker->text(rand(300, 600)) . '</p>';
        }

        $block = new TinymceBlock();
        $block->setHtml($html);

        $content = new Content();
        $content
            ->setVersion(0)
            ->addBlock($block)
        ;

        $subject->addContent($content);
    }

    /**
     * Associates tags to the given subject.
     *
     * @param TagSubjectInterface $subject
     */
    protected function generateTags(TagSubjectInterface $subject)
    {
        $qb = $this->container->get('ekyna_cms.tag.repository')->createQueryBuilder('t');
        $tags = $qb
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults(rand(1,4))
            ->getQuery()
            ->getResult()
        ;
        $subject->setTags(new ArrayCollection($tags));
    }

    /**
     * Returns the string representation of the given object.
     *
     * @param $object
     * @return string
     */
    protected function objectToString($object)
    {
        $r = new \ReflectionClass(get_class($object));

        foreach (array('getName', 'getTitle') as $getter) {
            if ($r->hasMethod($getter)) {
                try {
                    return $object->{$getter}();
                } catch(\Exception $e) {
                }
            }
        }

        if ($r->hasMethod('__toString')) {
            return (string) $object;
        }

        return '';
    }
}
