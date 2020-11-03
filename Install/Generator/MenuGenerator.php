<?php

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MenuGenerator
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuGenerator
{
    /**
     * @var \Ekyna\Component\Resource\Operator\ResourceOperatorInterface
     */
    private $operator;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Repository\MenuRepository
     */
    private $repository;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var OutputInterface
     */
    private $output;


    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param OutputInterface    $output
     */
    public function __construct(ContainerInterface $container, OutputInterface $output)
    {
        $this->output = $output;

        $this->operator = $container->get('ekyna_cms.menu.operator');
        $this->repository = $container->get('ekyna_cms.menu.repository');
        $this->config = $container->getParameter('ekyna_cms.menu.config');
        $this->locales = $container->getParameter('locales');
    }

    /**
     * Generates the menus based on configuration.
     */
    public function generateMenus()
    {
        foreach ($this->config['roots'] as $name => $config) {
            $this->output->write(sprintf(
                '- <comment>%s</comment> %s ',
                $name,
                str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT)
            ));

            if (null !== $menu = $this->repository->findOneByName($name)) {
                $this->output->writeln('already exists.');
                continue;
            }

            /** @var \Ekyna\Bundle\CmsBundle\Entity\Menu $menu */
            $menu = $this->repository->createNew();
            $menu
                ->setName($name)
                ->setDescription($config['description'])
                ->setLocked(true);

            foreach ($this->locales as $locale) {
                $menuTranslation = $menu->translate($locale, true);
                $menuTranslation
                    ->setTitle($config['title']);
            }

            $this->operator->persist($menu);

            $this->output->writeln('created.');
        }
    }
}
