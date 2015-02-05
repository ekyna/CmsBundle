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
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\MenuRepository
     */
    private $repository;

    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param OutputInterface $output
     */
    public function __construct(ContainerInterface $container, OutputInterface $output)
    {
        $this->output = $output;

        $this->em = $container->get('ekyna_cms.page.manager');
        $this->repository = $container->get('ekyna_cms.menu.repository');
        $this->config = $container->getParameter('ekyna_cms.menu.config');
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
                ->setTitle($config['title'])
                ->setDescription($config['description'])
                ->setLocked(true)
            ;

            $this->em->persist($menu);
            $this->output->writeln('done.');
        }
        $this->em->flush();
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
