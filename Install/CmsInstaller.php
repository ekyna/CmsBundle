<?php

namespace Ekyna\Bundle\CmsBundle\Install;

use Ekyna\Bundle\CmsBundle\Install\Generator\MenuGenerator;
use Ekyna\Bundle\CmsBundle\Install\Generator\PageGenerator;
use Ekyna\Bundle\InstallBundle\Install\OrderedInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CmsInstaller
 * @package Ekyna\Bundle\CmsBundle\Install
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsInstaller implements OrderedInstallerInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Command $command, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>[CMS] Generating menus:</info>');
        $menuGenerator = new MenuGenerator($this->container, $output);
        $menuGenerator->generateMenus();
        $output->writeln('');

        $output->writeln('<info>[CMS] Generating pages based on routing configuration:</info>');
        $pageGenerator = new PageGenerator($this->container, $output);
        $pageGenerator->generatePages();
        $output->writeln('');
    }

    public function getOrder()
    {
        return 512;
    }
}
