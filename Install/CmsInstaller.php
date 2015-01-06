<?php

namespace Ekyna\Bundle\CmsBundle\Install;

use Ekyna\Bundle\CmsBundle\Install\Generator\MenuGenerator;
use Ekyna\Bundle\CmsBundle\Install\Generator\PageGenerator;
use Ekyna\Bundle\InstallBundle\Install\OrderedInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CmsInstaller
 * @package Ekyna\Bundle\CmsBundle\Install
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsInstaller implements OrderedInstallerInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ContainerInterface $container, Command $command, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>[CMS] Generating menus:</info>');
        $menuGenerator = new MenuGenerator($container, $output);
        $menuGenerator->generateMenus();
        $output->writeln('');

        $output->writeln('<info>[CMS] Generating pages based on routing configuration:</info>');
        $pageGenerator = new PageGenerator($container, $output);
        $pageGenerator->generatePages();
        $output->writeln('');
    }

    public function getOrder()
    {
        return 512;
    }
}
