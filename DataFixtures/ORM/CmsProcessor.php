<?php

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Entity as CmsEntity;
use Ekyna\Bundle\CmsBundle\Model as CmsModel;
use Ekyna\Bundle\MediaBundle\Entity as MediaEntity;
use Ekyna\Bundle\MediaBundle\Model as MediaModel;
use Faker\Factory;
use Nelmio\Alice\ProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

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

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var array
     */
    protected $images;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->faker = Factory::create($container->getParameter('hautelook_alice.locale'));

        $this->loadImagesFiles();
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess($object)
    {
        if ($object instanceof CmsModel\SeoSubjectInterface) {
            $this->generateSeo($object);
        }
        if ($object instanceof CmsModel\ContentSubjectInterface) {
            $this->generateContent($object);
        }
        if ($object instanceof CmsModel\TagsSubjectInterface) {
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
     * @param CmsModel\SeoSubjectInterface $subject
     */
    protected function generateSeo(CmsModel\SeoSubjectInterface $subject)
    {
        $seo = new CmsEntity\Seo(); // TODO use repo::createNew (translations)
        if (0 < strlen($name = $this->objectToString($subject))) {
            $seo
                ->setTitle($name . ' seo title')
                ->setDescription($name . ' seo description');
        } else {
            $seo
                ->setTitle($this->faker->sentence(rand(3, 6)))
                ->setDescription($this->faker->words(rand(3, 6)));
        }
        $subject->setSeo($seo);
    }

    /**
     * Generates content to the given subject.
     *
     * @param CmsModel\ContentSubjectInterface $subject
     */
    protected function generateContent(CmsModel\ContentSubjectInterface $subject)
    {
        $html = '';
        for ($i = 0; $i < rand(3, 5); $i++) {
            $html .= '<p>' . $this->faker->text(rand(300, 600)) . '</p>';
        }

        $block = new CmsEntity\TinymceBlock(); // TODO use repo::createNew (translations)
        $block->setHtml($html);

        $content = new CmsEntity\Content();
        $content->addBlock($block);

        $subject->setContent($content);
    }

    /**
     * Associates tags to the given subject.
     *
     * @param CmsModel\TagsSubjectInterface $subject
     */
    protected function generateTags(CmsModel\TagsSubjectInterface $subject)
    {
        $qb = $this->container->get('ekyna_cms.tag.repository')->createQueryBuilder('t');
        $tags = $qb
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults(rand(1, 4))
            ->getQuery()
            ->getResult();
        $subject->setTags(new ArrayCollection($tags));
    }

    /**
     * Find some images.
     *
     * @param int $limit
     * @return MediaEntity\Image[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function findImages($limit = 1)
    {
        if ($limit < 1) {
            $limit = 1;
        }

        $images = [];

        if (rand(0, 10) > 5) {
            $qb = $this->container
                ->get('ekyna_media.image.repository')
                ->createQueryBuilder('i');

            $query = $qb
                ->addSelect('RAND() as HIDDEN rand')
                ->orderBy('rand')
                ->setMaxResults($limit)
                ->getQuery();

            if ($limit == 1 && null !== $image = $query->getOneOrNullResult()) {
                $images = array($image);
            } else {
                $images = $query->getResult();
            }
        }

        if (count($images) < $limit) {
            $images = array_merge($images, $this->createImages($limit - count($images)));
        }

        return $images;
    }

    /**
     * Creates some images.
     *
     * @param int $number
     * @return array
     */
    private function createImages($number = 1)
    {
        $images = [];

        for ($j = 0; $j < $number; $j++) {
            $image = new MediaEntity\Image();
            $image
                ->setFile($this->images[rand(0, 5)])
                ->setRename($this->faker->sentence(3))
                ->setAlt($this->faker->sentence(5));

            $images[] = $image;
        }

        return $images;
    }

    /**
     * Loads the fixtures images files.
     */
    private function loadImagesFiles()
    {
        $this->images = [];
        $imagesDir = realpath(__DIR__.'/../../Resources/fixtures/images/');
        for ($i = 1; $i < 7; $i++) {
            $this->images[] = new File($imagesDir.'/0'.$i.'.jpg');
        }
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

        if ($r->hasMethod('__toString')) {
            return (string)$object;
        }

        foreach (array('getName', 'getTitle') as $getter) {
            if ($r->hasMethod($getter)) {
                try {
                    return $object->{$getter}();
                } catch (\Exception $e) {
                }
            }
        }

        return '';
    }
}
