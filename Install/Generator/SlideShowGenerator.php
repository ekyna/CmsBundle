<?php

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SlideShowGenerator
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SlideShowGenerator
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var \Ekyna\Component\Resource\Operator\ResourceOperatorInterface
     */
    private $operator;

    /**
     * @var \Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface
     */
    private $repository;

    /**
     * @var array
     */
    private $names;


    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param OutputInterface    $output
     */
    public function __construct(ContainerInterface $container, OutputInterface $output)
    {
        $this->output = $output;

        $this->operator = $container->get('ekyna_cms.slide_show.operator');
        $this->repository = $container->get('ekyna_cms.slide_show.repository');
        $this->names = $container->getParameter('ekyna_cms.slide_show.static');
    }

    /**
     * Generates the slide shows based on configuration.
     */
    public function generateSlideShows()
    {
        foreach ($this->names as $tag => $name) {
            $this->output->write(sprintf(
                '- <comment>%s</comment> %s ',
                $name,
                str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT)
            ));

            if (null !== $slideShow = $this->findSlideShowByTag($tag)) {
                $this->output->writeln('already exists.');
                continue;
            }

            /** @var \Ekyna\Bundle\CmsBundle\Entity\SlideShow $slideShow */
            $slideShow = $this->repository->createNew();
            $slideShow
                ->setName($name)
                ->setTag($tag);

            $this->operator->persist($slideShow);

            $this->output->writeln('created.');
        }
    }

    /**
     * Finds this slide show by its name.
     *
     * @param string $tag
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\SlideShow|null
     */
    public function findSlideShowByTag($tag)
    {
        return $this->repository->findOneBy(['tag' => $tag]);
    }
}
