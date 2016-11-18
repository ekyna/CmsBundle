<?php

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Entity as CmsEntity;
use Ekyna\Bundle\CmsBundle\Model as CmsModel;
use Ekyna\Bundle\MediaBundle\Entity as MediaEntity;
use Ekyna\Bundle\MediaBundle\Model as MediaModel;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface;
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

    /**
     * @var ResourceRepositoryInterface
     */
    protected $seoRepository;

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
        $this->seoRepository = $container->get('ekyna_cms.seo.repository');
        $this->faker = Factory::create($container->getParameter('hautelook_alice.locale'));
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess($object)
    {
        if ($object instanceof CmsModel\SeoSubjectInterface) {
            $this->generateSeo($object);
        }
        /*if ($object instanceof CmsModel\ContentSubjectInterface) {
            $this->generateContent($object);
        }*/
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
        $seo = $this->seoRepository->createNew();
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
    /*protected function generateContent(CmsModel\ContentSubjectInterface $subject)
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
    }*/

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

        foreach (['getName', 'getTitle'] as $getter) {
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
