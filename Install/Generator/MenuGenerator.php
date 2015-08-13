<?php

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Ekyna\Bundle\CmsBundle\Entity\Menu;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MenuGenerator
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuGenerator
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var \Ekyna\Bundle\AdminBundle\Operator\ResourceOperatorInterface
     */
    private $menuOperator;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\MenuRepository
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
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param OutputInterface $output
     */
    public function __construct(ContainerInterface $container, OutputInterface $output)
    {
        $this->output = $output;

        $this->menuOperator = $container->get('ekyna_cms.menu.operator');
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

            if (null !== $menu = $this->findMenuByName($name)) {
                $this->output->writeln('already exists.');
                continue;
            }

            /** @var \Ekyna\Bundle\CmsBundle\Entity\Menu $menu */
            $menu = $this->repository->createNew();
            $menu
                ->setName($name)
                ->setDescription($config['description'])
                ->setLocked(true)
            ;

            foreach ($this->locales as $locale) {
                $menuTranslation = $menu->translate($locale, true);
                $menuTranslation
                    ->setTitle($config['title'])
                ;
            }

            $this->menuOperator->persist($menu);

            $this->output->writeln('done.');
        }
    }

    /**
     * Finds a menu by his name.
     *
     * @param $name
     * @return Menu|null
     */
    public function findMenuByName($name)
    {
        return $this->repository->findOneBy(array('name' => $name));
    }
}
