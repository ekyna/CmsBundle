<?php

namespace Ekyna\Bundle\CmsBundle\DataFixtures\ORM;

use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;
use Ekyna\Bundle\CmsBundle\Repository\SeoRepository;
use Ekyna\Bundle\CoreBundle\DataFixtures\ORM\Fixtures;
use Fidry\AliceDataFixtures\ProcessorInterface;

/**
 * Class CmsProcessor
 * @package Ekyna\Bundle\CmsBundle\DataFixtures\ORM
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CmsProcessor implements ProcessorInterface
{
    /**
     * @var SeoRepository
     */
    private $seoRepository;


    /**
     * Constructor.
     *
     * @param SeoRepository $seoRepository
     */
    public function __construct(SeoRepository $seoRepository)
    {
        $this->seoRepository = $seoRepository;
    }

    /**
     * @inheritDoc
     */
    public function preProcess(string $id, $object): void
    {
        if (!$object instanceof SeoSubjectInterface) {
            return;
        }

        /** @var SeoInterface $seo */
        $seo = $this->seoRepository->createNew();
        $seo
            ->setTitle(Fixtures::getFaker()->sentence())
            ->setDescription(Fixtures::getFaker()->sentence());
    }

    /**
     * @inheritDoc
     */
    public function postProcess(string $id, $object): void
    {

    }
}
