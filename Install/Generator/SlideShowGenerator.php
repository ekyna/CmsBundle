<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Ekyna\Bundle\CmsBundle\Model\SlideShowInterface;
use Ekyna\Component\Resource\Factory\ResourceFactoryInterface;
use Ekyna\Component\Resource\Manager\ResourceManagerInterface;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SlideShowGenerator
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SlideShowGenerator
{
    private ResourceManagerInterface    $manager;
    private ResourceRepositoryInterface $repository;
    private ResourceFactoryInterface    $factory;
    private array                       $names;


    /**
     * Constructor.
     *
     * @param ResourceManagerInterface    $manager
     * @param ResourceRepositoryInterface $repository
     * @param ResourceFactoryInterface    $factory
     * @param array                       $names
     */
    public function __construct(
        ResourceManagerInterface $manager,
        ResourceRepositoryInterface $repository,
        ResourceFactoryInterface $factory,
        array $names
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->names = $names;
    }

    /**
     * Generates the slide shows based on configuration.
     *
     * @param OutputInterface $output
     */
    public function generate(OutputInterface $output): void
    {
        foreach ($this->names as $tag => $name) {
            $output->write(sprintf(
                '- <comment>%s</comment> %s ',
                $name,
                str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT)
            ));

            if (null !== $this->findSlideShowByTag($tag)) {
                $output->writeln('already exists.');

                continue;
            }

            /** @var SlideShowInterface $slideShow */
            $slideShow = $this->factory->create();
            $slideShow
                ->setName($name)
                ->setTag($tag);

            $this->manager->save($slideShow);

            $output->writeln('created.');
        }
    }

    /**
     * Finds this slide show by its name.
     *
     * @param string $tag
     *
     * @return SlideShowInterface|null
     */
    public function findSlideShowByTag(string $tag): ?SlideShowInterface
    {
        return $this->repository->findOneBy(['tag' => $tag]);
    }
}
