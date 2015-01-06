<?php

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Install\Generator\PageGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeneratePagesCommand
 * @package Ekyna\Bundle\CmsBundle\Command
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GeneratePagesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ekyna:cms:generate-pages')
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'Whether to first remove the pages or not.')
            ->setDescription('Generates CMS pages.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $truncate = $input->getOption('truncate');

        $output->writeln(sprintf('Loading pages with truncate <info>%s</info>.', $truncate ? 'true' : 'false'));

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

        $output->writeln('Generating pages based and routing configuration :');

        $generator = new PageGenerator($this->getContainer(), $output);
        $generator->generatePages();
    }

    /**
     * Removes all the pages.
     *
     * @param OutputInterface $output
     */
    private function truncate(OutputInterface $output)
    {
        $output->writeln('Removing pages ...');

        $em = $this->getContainer()->get('ekyna_cms.page.manager');
        $repository = $this->getContainer()->get('ekyna_cms.page.repository');

        $count = 0;
        $pages = $repository->findAll();
        foreach ($pages as $page) {
            $em->remove($page);
            $count++;
        }
        $em->flush();
        $em->clear();

        $class = $this->getContainer()->getParameter('ekyna_cms.page.class');
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

        $output->writeln(sprintf('<info>%s</info> pages removed.', $count));
    }
}
