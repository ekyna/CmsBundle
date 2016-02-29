<?php

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Install\Generator\PageGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FixTreeCommand
 * @package Ekyna\Bundle\CmsBundle\Command
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class FixTreeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ekyna:cms:fix-tree')
            ->setDescription('Fix the pages tree.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $pageRepository = $this->getContainer()->get('ekyna_cms.page.repository');

        $output->writeln('Page tree');
        $output->write(' - checking ... ');

        if (true !== $pageRepository->verify()) {
            $output->writeln('error');
            $output->write(' - recovering ... ');
            $pageRepository->recover();
            $em->flush();
            $output->writeln('ok');
        } else {
            $output->writeln('ok');
        }

        $menuRepository = $this->getContainer()->get('ekyna_cms.menu.repository');

        $output->writeln('Menu tree');
        $output->write(' - checking ... ');

        if (true !== $menuRepository->verify()) {
            $output->writeln('error');
            $output->write(' - recovering ... ');
            $menuRepository->recover();
            $em->flush();
            $output->writeln('ok');
        } else {
            $output->writeln('ok');
        }
    }
}
