<?php

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Install\Generator\MenuGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateMenusCommand
 * @package Ekyna\Bundle\CmsBundle\Command
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GenerateMenusCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ekyna:cms:generate-menus')
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'Whether to first remove the menus or not.')
            ->setDescription('Generates CMS menus.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $truncate = $input->getOption('truncate');

        $output->writeln(sprintf('Loading menus with truncate <info>%s</info>.', $truncate ? 'true' : 'false'));

        /** @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation(
            $output,
            '<question>Do you want to continue ? (y/n)[Y]</question>',
            true
        )
        ) {
            return;
        }

        if ($truncate) {
            $this->truncate($output);
        }

        $output->writeln('Generating menus based and routing configuration :');

        $generator = new MenuGenerator($this->getContainer(), $output);
        $generator->generateMenus();
    }

    /**
     * Removes all the menus.
     *
     * @param OutputInterface $output
     */
    private function truncate(OutputInterface $output)
    {
        $output->writeln('Removing menus ...');

        $em = $this->getContainer()->get('ekyna_cms.menu.manager');
        $repository = $this->getContainer()->get('ekyna_cms.menu.repository');

        $count = 0;
        $menus = $repository->findAll();
        foreach ($menus as $menu) {
            $em->remove($menu);
            $count++;
        }
        $em->flush();
        $em->clear();

        $class = $this->getContainer()->getParameter('ekyna_cms.menu.class');
        $cmd = $em->getClassMetadata($class);
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Failed to truncate table for class %s.</error>', $class));
            $connection->rollback();
        }

        $output->writeln(sprintf('<info>%s</info> menus removed.', $count));
    }
}
