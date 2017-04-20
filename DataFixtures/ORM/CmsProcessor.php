<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Ekyna\Bundle\CmsBundle\Factory\SeoFactoryInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;
use Faker\Factory;
use Fidry\AliceDataFixtures\ProcessorInterface;

/**
 * Class CmsProcessor
 * @package Ekyna\Bundle\CmsBundle\DataFixtures\ORM
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CmsProcessor implements ProcessorInterface
{
    private SeoFactoryInterface $seoFactory;


    /**
     * Constructor.
     *
     * @param SeoFactoryInterface $seoFactory
     */
    public function __construct(SeoFactoryInterface $seoFactory)
    {
        $this->seoFactory = $seoFactory;
    }

    /**
     * @inheritDoc
     */
    public function preProcess(string $id, $object): void
    {
        // TODO ContentSubjectInterface

        if (!$object instanceof SeoSubjectInterface) {
            return;
        }

        $seo = $this->seoFactory->create();

        $faker = Factory::create();

        $seo
            ->setTitle($faker->sentence())
            ->setDescription($faker->sentence());

        $object->setSeo($seo);
    }

    /**
     * @inheritDoc
     */
    public function postProcess(string $id, $object): void
    {
    }
}
